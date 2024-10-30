<?php

namespace Kiskadi\Admin;

use Exception;
use Kiskadi\Api\Resources\Branches;
use Kiskadi\Api\Resources\Vendors;
use Kiskadi\Api\Response\Error_Response;
use WC_Admin_Settings;

class Kiskadi_Integration_Admin extends \WC_Integration {

	public function __construct() {
		$this->id                 = 'kiskadi-integration';
		$this->method_title       = __( 'Kiskadi Integration' );
		$this->method_description = __( 'Enable integration with Kiskadi', 'woocommerce-integration-demo' );
		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();
		// Define user set variables.
		$this->kiskadi_user     = $this->get_option( 'kiskadi_user' );
		$this->kiskadi_password = $this->get_option( 'kiskadi_password' );
		$this->debug            = $this->get_option( 'debug' );
		// Actions.
		add_action( 'woocommerce_update_options_integration_' . $this->id, array( $this, 'process_admin_options' ) );
	}


	public function init_form_fields() {
		$this->form_fields = array(
			'user'     => array(
				'title'       => __( 'User' ),
				'type'        => 'text',
				'description' => __( 'Enter with your user login from kiskadi', 'kiskadi' ),
				'desc_tip'    => true,
				'default'     => '',
			),
			'password' => array(
				'title'       => __( 'Password' ),
				'type'        => 'password',
				'description' => __( 'Enter with your password login from kiskadi. You can find this in "User Profile" drop-down (top right corner) > API Keys.', 'kiskadi' ),
				'desc_tip'    => true,
				'default'     => '',
			),
			'debug'    => array(
				'title'       => __( 'Debug Log' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable logging', 'kiskadi' ),
				'default'     => 'no',
				'description' => __( 'Log events such as API requests', 'kiskadi' ),
			),
		);
	}

	public function process_admin_options() : void {
		parent::process_admin_options();

		try {
			$this->try_save_branch();
		} catch ( Exception $e ) {
			$message = $e->getMessage();
			delete_option( $this->get_option_key() );
			WC_Admin_Settings::add_error( $message );
		}

	}

	private function try_save_branch() : bool {
		$vendors = ( new Vendors() )->all();

		if ( is_a( $vendors, Error_Response::class ) ) {
			$error_message = $vendors->get_first_error_message();
			throw new Exception( $error_message );
		}

		if ( ! isset( $vendors->items[0] ) ) {
			$error_message = $vendors->get_first_error_message();
			throw new Exception( __( 'No vendors found', 'kiskadi' ) );
		}

		$vendor    = $vendors->items[0];
		$vendor_id = $vendor->id;

		$filter_branches = array(
			'vendor_id' => $vendor_id,
		);

		$branches = ( new Branches() )->all( $filter_branches );

		if ( is_a( $branches, Error_Response::class ) ) {
			$error_message = $branches->get_first_error_message();
			throw new Exception( $error_message );
		}

		if ( ! isset( $branches->items[0] ) ) {
			throw new Exception( __( 'No branches found', 'kiskadi' ) );
		}

		$branch    = $branches->items[0];
		$branch_id = $branch->id;

		$this->settings['vendor_id'] = $vendor_id;
		$this->settings['branch_id'] = $branch_id;

		$option_key = $this->get_option_key();
		do_action( 'woocommerce_update_option', array( 'id' => $option_key ) );
		return update_option( $option_key, apply_filters( 'woocommerce_settings_api_sanitized_fields_' . $this->id, $this->settings ), 'yes' );
	}
}
