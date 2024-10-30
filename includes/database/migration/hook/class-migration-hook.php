<?php

namespace Kiskadi\Database\Migration\Hook;

use Exception;
use Kiskadi\Database\Migration\Recipe\Migration_1_1_0;

class Migration_Hook {

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
		Install_Package_Hook::instance()->init();
		Legacy_Migration_Hook::instance()->init();
	}
}
