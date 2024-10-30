<?php

namespace Kiskadi\Integration\WooCommerce\Log;

use Kiskadi\Integration\WooCommerce\Admin\Kiskadi_Integration_Admin;

class Logger {

	/** @var Kiskadi_Integration_Admin */
	private $settings;

	/** @var \WC_Logger */
	private $logger;

	/** @var string */
	private $source;

	public function __construct() {
		$this->settings = new Kiskadi_Integration_Admin();
		$this->logger   = wc_get_logger();
		$this->source   = 'kiskadi';
	}

	/**
	 * @param string $message
	 * @param string $level
	 */
	public function log( $message, $level = 'info' ) : void {
		if ( 'no' === $this->settings->get_option( 'debug' ) ) {
			return;
		}

		$this->logger->log( $level, $message, array( 'source' => $this->source ) );
	}
}
