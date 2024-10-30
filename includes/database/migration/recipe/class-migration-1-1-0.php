<?php

// phpcs:disable WordPress.DB

namespace Kiskadi\Database\Migration\Recipe;

use Exception;
use Kiskadi\Database\Migration\Recipe_Interface;
use Kiskadi\Vendor\Api\Branch_Client;
use Kiskadi\Vendor\Repository\Branch_Repository;

class Migration_1_1_0 implements Recipe_Interface {

	public function up() : void {
		global $wpdb;

		$kiskadi_settings_option_name = 'woocommerce_kiskadi_settings';

		$wpdb->update(
			"{$wpdb->prefix}options",
			array( 'option_name' => $kiskadi_settings_option_name ),
			array( 'option_name' => 'woocommerce_kiskadi-integration_settings' )
		);

		try {
			$notoptions = wp_cache_get( 'notoptions', 'options' );
			if ( is_array( $notoptions ) ) {
				unset( $notoptions[ $kiskadi_settings_option_name ] );
				wp_cache_set( 'notoptions', $notoptions, 'options' );
			}

			$branch_data = ( new Branch_Client() )->first();
			( new Branch_Repository() )->update( $branch_data );

			$kiskadi_settings_option_value = get_option( $kiskadi_settings_option_name );

			if ( ! is_array( $kiskadi_settings_option_value ) ) {
				return;
			}

			unset( $kiskadi_settings_option_value['vendor_id'] );
			unset( $kiskadi_settings_option_value['branch_id'] );

			$kiskadi_settings_option_value['enable_cashback'] = 'yes';

			update_option( $kiskadi_settings_option_name, $kiskadi_settings_option_value );
		} catch ( Exception $e ) {
			$wpdb->delete(
				"{$wpdb->prefix}options",
				array( 'option_name' => $kiskadi_settings_option_name )
			);
		}

		$wpdb->update(
			"{$wpdb->prefix}postmeta",
			array( 'meta_key' => '_kiskadi_transaction_id' ),
			array( 'meta_key' => 'kiskadi_point_id' )
		);
	}

	public function down() : void {
		global $wpdb;

		$kiskadi_settings_option_name = 'woocommerce_kiskadi-integration_settings';

		$wpdb->update(
			"{$wpdb->prefix}options",
			array( 'option_name' => $kiskadi_settings_option_name ),
			array( 'option_name' => 'woocommerce_kiskadi_settings' )
		);

		$kiskadi_settings_option_value = get_option( $kiskadi_settings_option_name );

		if ( is_array( $kiskadi_settings_option_value ) ) {
			unset( $kiskadi_settings_option_value['enable_cashback'] );
		}

		update_option( $kiskadi_settings_option_name, $kiskadi_settings_option_value );

		$wpdb->delete(
			"{$wpdb->prefix}options",
			array( 'option_name' => 'kiskadi_branch_id' )
		);

		$wpdb->delete(
			"{$wpdb->prefix}options",
			array( 'option_name' => 'kiskadi_branch_cnpj' )
		);

		$wpdb->update(
			"{$wpdb->prefix}postmeta",
			array( 'meta_key' => 'kiskadi_point_id' ),
			array( 'meta_key' => '_kiskadi_transaction_id' )
		);
	}
}
