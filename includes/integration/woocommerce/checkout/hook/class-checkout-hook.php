<?php

namespace Kiskadi\Integration\WooCommerce\Checkout\Hook;

use Exception;
use Kiskadi\Integration\WooCommerce\Checkout\Hook\Assets\Enqueue_Scripts_Hook;
use Kiskadi\Integration\WooCommerce\Checkout\Hook\Cashback\Cart_Calculate_Fees_Hook;
use Kiskadi\Integration\WooCommerce\Checkout\Hook\Cashback\Cashback_Available_Hook;
use Kiskadi\Integration\WooCommerce\Checkout\Hook\Cashback\Checkout_Order_Processed_Hook;

class Checkout_Hook {

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
		Enqueue_Scripts_Hook::instance()->init();
		Cart_Calculate_Fees_Hook::instance()->init();
		Cashback_Available_Hook::instance()->init();
		Checkout_Order_Processed_Hook::instance()->init();
	}

}
