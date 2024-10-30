<?php

namespace Kiskadi\Integration\WooCommerce\Checkout\Hook\Cashback;

use Exception;
use Kiskadi\Consumer\Point;
use Kiskadi\Integration\WooCommerce\Admin\Kiskadi_Integration_Admin;
use Kiskadi\Integration\WooCommerce\Checkout\Security\Checkout_Nonce;
use Kiskadi\Kiskadi;

class Cashback_Available_Hook {

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
		add_action( 'wp_ajax_kiskadi_cashback_available', array( $this, 'kiskadi_cashback_available' ) );
		add_action( 'wp_ajax_nopriv_kiskadi_cashback_available', array( $this, 'kiskadi_cashback_available' ) );
	}

	public function kiskadi_cashback_available() : void {
		$is_cashback_enable = ( new Kiskadi_Integration_Admin() )->is_cashback_enable();
		if ( false === $is_cashback_enable ) {
			wp_send_json_error();
		}

		if ( ! isset( $_POST['billing_cpf'], $_POST['wp_nonce'] ) ) {
			wp_send_json_error();
		}

		$nonce          = sanitize_text_field( wp_unslash( $_POST['wp_nonce'] ) );
		$action         = ( new Checkout_Nonce() )->action();
		$nonce_verified = wp_verify_nonce( $nonce, $action );
		if ( false === $nonce_verified ) {
			wp_send_json_error();
		}

		$billing_cpf = sanitize_text_field( wp_unslash( $_POST['billing_cpf'] ) );

		try {
			$cart_total                   = WC()->cart->get_cart_contents_total() + WC()->cart->get_shipping_total();
			$exchangeable_points_api_data = ( new Point() )->query_exchangeable( $cart_total, $billing_cpf );
		} catch ( Exception $e ) {
			wp_send_json_error();
		}

		if ( 0.00 === $exchangeable_points_api_data->available_discount() ) {
			wp_send_json_error();
		}

		$is_cashback_discount_active = false;
		if ( isset( $_POST['kiskadi_exchangeable_points'] ) && 'false' !== $_POST['kiskadi_exchangeable_points'] ) {
			$is_cashback_discount_active = true;
		}

		$data = array(
			'exchangeable_points' => $exchangeable_points_api_data,
			'has_cashback'        => $is_cashback_discount_active,
		);

		$exchangeable_points_html = Kiskadi::instance()->get_template_file( 'checkout/exchangeable-points.php', $data, true );

		wp_send_json_success(
			array(
				'template' => $exchangeable_points_html,
			)
		);
	}
}
