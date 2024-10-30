<?php

namespace Kiskadi\Integration\WooCommerce\Checkout\Hook\Assets;

use Exception;
use Kiskadi\Integration\WooCommerce\Checkout\Security\Checkout_Nonce;
use Kiskadi\Kiskadi;

class Enqueue_Scripts_Hook {

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
		add_action( 'wp_enqueue_scripts', array( $this, 'load_script' ) );
	}
	public function load_script() : void {
		if ( false === is_checkout() || true === is_wc_endpoint_url() ) {
			return;
		}

		$nonce = ( new Checkout_Nonce() )->create();

		$kiskadi_param = array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'wp_nonce' => $nonce,
		);

		wp_register_script( 'wc-endpoint-kiskadi', Kiskadi::instance()->plugin_url() . '/assets/js/frontend/checkout.js', array( 'jquery' ), Kiskadi::instance()->version, true );
		wp_localize_script( 'wc-endpoint-kiskadi', 'kiskadi_param', $kiskadi_param );
		wp_enqueue_script( 'wc-endpoint-kiskadi' );
	}
}
