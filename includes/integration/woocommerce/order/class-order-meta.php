<?php

namespace Kiskadi\Integration\WooCommerce\Order;

use Exception;
use WC_Order;

class Order_Meta {

	/** @var WC_Order */
	private $order;

	/** @var string */
	private $key;

	public function __construct( WC_Order $order, string $key ) {
		$this->order = $order;
		$this->key   = $key;
	}

	public function value_as_string( string $context = 'view' ) : string {
		$data = $this->order->get_meta( $this->key, true, $context );

		if ( false === is_string( $data ) ) {
			throw new Exception( "{$this->key} is not a string." );
		}

		$data = strval( $data );

		return $data;
	}
}
