<?php

namespace Kiskadi\Database\Migration;

use Exception;

if ( ! isset( $from_version, $to_version ) ) {
	throw new Exception( __( 'From and to version are required to create the migrator.', 'kiskadi' ) );
}

return new class( $from_version, $to_version ) implements Migrator_Interface {

	/** @var string */
	private $from_version;

	/** @var string */
	private $to_version;

	/** @var Recipe_Interface[] */
	private $recipes;

	/** @var Version_Compare */
	private $version_compare;

	public function __construct( string $from_version, string $to_version ) {
		$this->version_compare = new Version_Compare();
		$this->from_version    = $from_version;
		$this->to_version      = $to_version;
		$this->recipes         = $this->load_recipes( $this->from_version, $this->to_version, $this->version_compare );
	}

	/**
	 * @throws Exception Cannot read recipes directory
	 * @return Recipe_Interface[]
	 */
	private function load_recipes( string $from_version, string $to_version, Version_Compare $version_compare ) : array {
		$recipes           = array();
		$recipes_dir       = __DIR__ . '/recipe';
		$recipes_dir_files = scandir( $recipes_dir );
		if ( false === $recipes_dir_files ) {
			throw new Exception( __( 'Cannot read recipes directory.', 'kiskadi' ) );
		}

		$recipes_files = array_diff( $recipes_dir_files, array( '.', '..' ) );

		foreach ( $recipes_files as $recipe_file ) {
			$version = $this->extract_version_from_recipe_file_name( $recipe_file );

			if ( $version_compare->is_out_of_boundary_version( $version, $from_version, $to_version ) ) {
				continue;
			}

			$migration_class_name = __NAMESPACE__ . '\Recipe\Migration_' . str_replace( '.', '_', $version );
			$migration_recipe     = new $migration_class_name();

			if ( false === ( $migration_recipe instanceof Recipe_Interface ) ) {
				/* translators: migration class name */
				throw new Exception( sprintf( __( 'Recipe %s does not implement Recipe_Interface', 'kiskadi' ), $migration_class_name ) );
			}

			$recipes[] = $migration_recipe;
		}

		return $recipes;
	}

	private function extract_version_from_recipe_file_name( string $file_name ) : string {
		$version_part = preg_replace( '/class-migration-(.*)\.php/', '$1', $file_name );

		if ( ! is_string( $version_part ) ) {
			throw new Exception( __( 'Cannot extract version from file name.', 'kiskadi' ) );
		}

		$version = str_replace( '-', '.', $version_part );

		return $version;
	}

	public function migrate() : void {
		if ( $this->version_compare->is_upgrade( $this->from_version, $this->to_version ) ) {
			require_once __DIR__ . '/migration-autoload.php';
			$this->up();
			return;
		}

		$this->down();
	}

	private function up() : void {
		foreach ( $this->recipes as $recipe ) {
			call_user_func( array( $recipe, 'up' ) );
		}
	}

	private function down() : void {
		foreach ( array_reverse( $this->recipes ) as $recipe ) {
			call_user_func( array( $recipe, 'down' ) );
		}
	}
};
