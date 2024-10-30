<?php

namespace Kiskadi\Integration\WooCommerce\Checkout\Hook\Cashback;

use Exception;
use Kiskadi\Consumer\Point;
use Kiskadi\Integration\WooCommerce\Checkout\Helper\Cashback_Helper;
use Kiskadi\Integration\WooCommerce\Order\Order_Consumer_Extractor;
use Kiskadi\Integration\WooCommerce\Order\Order_Item_Product_Extractor;
use Kiskadi\Integration\WooCommerce\Repository\Customer_Repository;
use Kiskadi\Integration\WooCommerce\Repository\Order_Repository;
use Kiskadi\Transaction\Api\Transaction_Client;
use Kiskadi\Transaction\Data\Transaction_Exchange_Data;
use Kiskadi\Vendor\Repository\Branch_Repository;
use WC_Cart;
use WC_Order;

class Checkout_Order_Processed_Hook {

	/** @var self */
	protected static $instance = null;

	private function __construct() {
	}

	private function __clone() {
	}

	public function __wakeup() {
		throw new Exception( __( 'Cannot unserialize singleton', 'kiskadi' ) );
	}

	public static function instance() : self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function init() : void {
		add_action( 'woocommerce_checkout_order_processed', array( $this, 'exchange_points' ), 10, 3 );
	}

	/**
	 * @param array<mixed> $posted_data
	 * @throws Exception
	 */
	public function exchange_points( int $order_id, $posted_data, WC_Order $order ) : void {
		$exchangeble_point_data = ( new Cashback_Helper() )->exchangeble_point_data();

		if ( false === $exchangeble_point_data['exchange_point_enabled'] ) {
			return;
		}

		$billing_cpf     = $exchangeble_point_data['billing_cpf'];
		$cashback_amount = $exchangeble_point_data['cashback_amount'];
		$order_total     = $order->get_total() + $cashback_amount;

		$exchangeable_points_api_data = ( new Point() )->query_exchangeable( $order_total, $billing_cpf );
		if ( $cashback_amount !== $exchangeable_points_api_data->available_discount() ) {
			throw new Exception( __( 'Invalid cashback amount.', 'kiskadi' ) );
		}

		$points_to_exchange = $exchangeable_points_api_data->points_to_exchange();
		$this->send_points_to_exchange( $order, $points_to_exchange );
	}

	private function send_points_to_exchange( WC_Order $order, float $points_to_exchange ) : void {
		$branch_data = ( new Branch_Repository() )->get_branch();
		if ( null === $branch_data ) {
			return;
		}

		$consumer    = ( new Order_Consumer_Extractor( $order ) )->extract();
		$spent_value = $order->get_total();
		$products    = ( new Order_Item_Product_Extractor( $order ) )->extract();

		$transaction_data     = new Transaction_Exchange_Data( $consumer, $points_to_exchange, $spent_value, $branch_data, $products );
		$transaction_api_data = ( new Transaction_Client() )->create_exchange( $transaction_data );

		$customer_id = $order->get_customer_id();
		$user        = get_user_by( 'id', $customer_id );
		if ( false !== $user ) {
			( new Customer_Repository() )->update_kiskadi_consumer_id( $user, $transaction_api_data->consumer()->id() );
		}

		( new Order_Repository() )->update_exchangeable_points( $order, $points_to_exchange );
	}
}
