<?php

namespace Kiskadi\Database\Migration\Hook;

use Exception;
use Kiskadi\Database\Migration\Migrator;
use Kiskadi\Database\Migration\Migrator_Interface;
use Kiskadi\Database\Migration\Migrator_Loader;
use Kiskadi\Kiskadi;
use WP_Upgrader;

class Install_Package_Hook {

	/** @var ?Migrator_Interface */
	private $migrator = null;

	/** @var Migrator_Loader */
	private $migrator_loader = null;


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
		add_filter( 'upgrader_source_selection', array( $this, 'prepare_migration' ), 10, 4 );
		add_filter( 'upgrader_post_install', array( $this, 'execute_migration' ), 10, 3 );
	}

	/** @param array<mixed> $args */
	public function prepare_migration( string $source, string $remote_source, WP_Upgrader $upgrader, array $args ) : string {
		$plugin_name = basename( $source );
		if ( 'kiskadi' !== $plugin_name ) {
			return $source;
		}

		$current_plugin_path   = Kiskadi::instance()->plugin_path();
		$new_plugin_path       = $source;
		$this->migrator_loader = new Migrator_Loader( $current_plugin_path, $new_plugin_path );

		$is_downgrade = $this->migrator_loader->is_downgrade();
		if ( $is_downgrade ) {
			$this->migrator = $this->load_migrator();
		}

		return $source;
	}

	private function load_migrator() : ?Migrator_Interface {
		try {
			return $this->migrator_loader->load();
		} catch ( Exception $e ) {
			return null;
		}
	}

	/**
	 * @param array<mixed> $hook_extra
	 * @param array<mixed> $result
	 */
	public function execute_migration( bool $response, array $hook_extra, array $result ) : bool {
		if ( null === $this->migrator_loader ) {
			return $response;
		}

		$is_upgrade = $this->migrator_loader->is_upgrade();
		if ( $is_upgrade ) {
			$this->migrator = $this->load_migrator();
		}

		if ( null === $this->migrator ) {
			return $response;
		}

		$this->migrator->migrate();

		return $response;
	}
}
