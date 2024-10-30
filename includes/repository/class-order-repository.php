<?php

namespace Kiskadi\Repository;

use WC_Order;

class Order_Repository {

	public function get_by_id( int $order_id ) : ?WC_Order {
		$order = wc_get_order( $order_id );

		if ( false === $order ) {
			return null;
		}

		return $order;
	}

	public function update_kiskadi_point_id( WC_Order $order, $point_id ) : void {
		update_post_meta( $order->get_id(), 'kiskadi_point_id', $point_id );
	}

	public function get_kiskadi_point_id( WC_Order $order ) : int {
		$kiskadi_point_id = get_post_meta( $order->get_id(), 'kiskadi_point_id', true );

		return intval( $kiskadi_point_id );
	}
}
