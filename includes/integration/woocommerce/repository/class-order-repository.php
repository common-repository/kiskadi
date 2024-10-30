<?php

namespace Kiskadi\Integration\WooCommerce\Repository;

use WC_Order;

class Order_Repository {

	/** @var string */
	private $kiskadi_transaction_id_key = '_kiskadi_transaction_id';

	/** @var string */
	private $kiskadi_exchangeable_points_key = '_kiskadi_exchangeable_points';

	public function update_kiskadi_transaction_id( WC_Order $order, int $transaction_id ) : void {
		update_post_meta( $order->get_id(), $this->kiskadi_transaction_id_key, $transaction_id );
	}

	public function get_kiskadi_transaction_id( WC_Order $order ) : int {
		$kiskadi_transaction_id = get_post_meta( $order->get_id(), $this->kiskadi_transaction_id_key, true );

		return intval( $kiskadi_transaction_id );
	}

	public function update_exchangeable_points( WC_Order $order, float $exchangeable_points ) : void {
		update_post_meta( $order->get_id(), $this->kiskadi_exchangeable_points_key, $exchangeable_points );
	}
}
