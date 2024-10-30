<?php

namespace Kiskadi\Database\Migration\Hook;

use Exception;
use Kiskadi\Database\Migration\Recipe\Migration_1_1_0;

class Legacy_Migration_Hook {

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
		add_action( 'admin_init', array( $this, 'ensure_legacy_migration' ) );
	}

	public function ensure_legacy_migration() : void {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		$legacy_migration_option_key = 'kiskadi_legacy_migration';

		$legacy_already_ran = get_option( $legacy_migration_option_key, false );
		if ( false !== $legacy_already_ran ) {
			return;
		}

		$migration = new Migration_1_1_0();
		$migration->up();

		add_option( $legacy_migration_option_key, 'yes', '', false );
	}
}
