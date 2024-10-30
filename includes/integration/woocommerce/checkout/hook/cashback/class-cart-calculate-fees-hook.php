<?php

namespace Kiskadi\Integration\WooCommerce\Checkout\Hook\Cashback;

use Exception;
use InvalidArgumentException;
use Kiskadi\Consumer\Point;
use Kiskadi\Integration\WooCommerce\Admin\Kiskadi_Integration_Admin;
use Kiskadi\Integration\WooCommerce\Checkout\Helper\Cashback_Helper;
use Kiskadi\Integration\WooCommerce\Fee\Cashback_Fee;
use WC_Cart;

class Cart_Calculate_Fees_Hook {

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
		add_action( 'woocommerce_cart_calculate_fees', array( $this, 'add_cashback_discount_to_cart' ), 10, 1 );
	}

	public function add_cashback_discount_to_cart( WC_Cart $cart ) : void {
		try {
			$exchangeble_point_data = ( new Cashback_Helper() )->exchangeble_point_data();
			$billing_cpf            = $exchangeble_point_data['billing_cpf'];
			$exchange_point_enabled = $exchangeble_point_data['exchange_point_enabled'];

			if ( false === $exchange_point_enabled ) {
				return;
			}

			$is_cashback_enable = ( new Kiskadi_Integration_Admin() )->is_cashback_enable();
			if ( false === $is_cashback_enable ) {
				return;
			}

			$cart_total                   = $cart->get_cart_contents_total() + $cart->get_shipping_total();
			$exchangeable_points_api_data = ( new Point() )->query_exchangeable( $cart_total, $billing_cpf );

			$discount = $exchangeable_points_api_data->available_discount();
			if ( 0.00 === $discount ) {
				return;
			}

			( new Cashback_Fee( $discount ) )->add_to_cart( $cart );
		} catch ( Exception $e ) {
			return;
		}
	}
}
