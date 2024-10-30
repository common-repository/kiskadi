<?php

namespace Kiskadi\I18n\Hook;

use Exception;
use Kiskadi\I18n\Textdomain;

class I18n_Hook {

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
		( new Textdomain() )->load_plugin_textdomain();
	}
}
