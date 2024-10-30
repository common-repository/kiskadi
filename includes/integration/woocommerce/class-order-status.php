<?php

namespace Kiskadi\Integration\WooCommerce;

use Exception;
use Kiskadi\Api\Auth\Admin_Auth;
use Kiskadi\Integration\WooCommerce\Admin\Kiskadi_Integration_Admin;
use Kiskadi\Integration\WooCommerce\Log\Logger;
use Kiskadi\Integration\WooCommerce\Order\Order_Consumer_Extractor;
use Kiskadi\Integration\WooCommerce\Order\Order_Item_Product_Extractor;
use Kiskadi\Integration\WooCommerce\Repository\Customer_Repository;
use Kiskadi\Integration\WooCommerce\Repository\Order_Repository;
use Kiskadi\Transaction\Api\Transaction_Client;
use Kiskadi\Transaction\Data\Transaction_Data;
use Kiskadi\Vendor\Data\Branch_Data;
use Kiskadi\Vendor\Repository\Branch_Repository;
use WC_Order;

class Order_Status {

	public function change_order_status( int $order_id, string $old_status, string $new_status ) : void {
		$is_paid_order = $this->is_paid_order( $new_status );
		if ( false === $is_paid_order ) {
			return;
		}

		$branch_data = ( new Branch_Repository() )->get_branch();
		if ( null === $branch_data ) {
			return;
		}

		$order = wc_get_order( $order_id );
		if ( false === $order instanceof WC_Order ) {
			return;
		}

		$kiskadi_transaction_id = ( new Order_Repository() )->get_kiskadi_transaction_id( $order );
		if ( 0 !== $kiskadi_transaction_id ) {
			return;
		}

        $is_transactions_enable = ( new Kiskadi_Integration_Admin() )->is_transactions_enable();
        if ( false === $is_transactions_enable ) {
            return;
        }

        $payment_methods_enable = ( new Kiskadi_Integration_Admin() )->payment_methods_enable();
        if (!in_array($order->get_payment_method(),$payment_methods_enable)) {
            return;
        }

		try {
			$this->send_order_data( $order, $branch_data );
		} catch ( Exception $e ) {
			$order_note = __( 'Kiskadi: An error occurred while sending points.', 'kiskadi' );
			$order->add_order_note( $order_note );
		}
	}

	private function send_order_data( WC_Order $order, Branch_Data $branch_data ) : void {
		$consumer    = ( new Order_Consumer_Extractor( $order ) )->extract();
		$spent_value = floatval( $order->get_total() );
		$products    = ( new Order_Item_Product_Extractor( $order ) )->extract();

		$transaction_data     = new Transaction_Data( $consumer, $spent_value, $branch_data, $products );
		$transaction_api_data = ( new Transaction_Client() )->create_point( $transaction_data );

		$customer_id = $order->get_customer_id();
		$user        = get_user_by( 'id', $customer_id );
		if ( false !== $user ) {
			( new Customer_Repository() )->update_kiskadi_consumer_id( $user, $transaction_api_data->consumer()->id() );
		}

		( new Order_Repository() )->update_kiskadi_transaction_id( $order, $transaction_api_data->id() );

		/* translators: %s kiskadi transaction points */
		$order_note = sprintf( __( 'Kiskadi: This order generated %s points.', 'kiskadi' ), $transaction_api_data->points() );
		$order->add_order_note( $order_note );
	}

	private function is_paid_order( string $new_status ) : bool {
		if ( 'completed' === $new_status ) {
			return true;
		}

		if ( 'processing' === $new_status ) {
			return true;
		}

		return false;
	}
}
