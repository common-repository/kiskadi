<?php

namespace Kiskadi\Database\Migration;

use Exception;

class Migrator_Loader {

	/** @var string */
	private $current_plugin_path;

	/** @var string */
	private $new_plugin_path;

	/** @var string */
	private $from_version;

	/** @var string */
	private $to_version;

	public function __construct( string $current_plugin_path, string $new_plugin_path ) {
		$this->current_plugin_path = $current_plugin_path;
		$this->new_plugin_path     = $new_plugin_path;
		$this->from_version        = $this->plugin_version_from_path( $current_plugin_path );
		$this->to_version          = $this->plugin_version_from_path( $new_plugin_path );
	}

	private function plugin_version_from_path( string $path ) : string {
		$plugin_main_file = $path . 'kiskadi.php';
		$version          = get_plugin_data( $plugin_main_file )['Version'];

		return $version;
	}

	public function is_downgrade() : bool {
		$is_downgrade = ( new Version_Compare() )->is_downgrade( $this->from_version, $this->to_version );
		return $is_downgrade;
	}

	public function is_upgrade() : bool {
		$is_upgrade = ( new Version_Compare() )->is_upgrade( $this->from_version, $this->to_version );
		return $is_upgrade;
	}

	public function load() : Migrator_Interface {
		$migrator_file          = 'includes/database/migration/class-migrator.php';
		$migrator_absolute_path = $this->current_plugin_path . $migrator_file;

		$is_upgrade = ( new Version_Compare() )->is_upgrade( $this->from_version, $this->to_version );
		if ( $is_upgrade ) {
			$migrator_absolute_path = $this->new_plugin_path . $migrator_file;
		}

		if ( ! is_readable( $migrator_absolute_path ) ) {
			throw new Exception( __( 'Migrator class is not readable.', 'kiskadi' ) );
		}

		$from_version = $this->from_version;
		$to_version   = $this->to_version;
		return require_once $migrator_absolute_path;
	}
};
