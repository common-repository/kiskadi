<?php

namespace Kiskadi\Integration\WooCommerce\Admin;

use Exception;
use Kiskadi\Api\Auth\Admin_Auth;
use Kiskadi\Integration\WooCommerce\Log\Logger;
use Kiskadi\Vendor\Api\Branch_Client;
use Kiskadi\Vendor\Repository\Branch_Repository;
use WC_Admin_Settings;

class Kiskadi_Integration_Admin extends \WC_Integration {

	public function __construct() {
		$this->id                 = 'kiskadi';
		$this->method_title       = __( 'Kiskadi' );
		$this->method_description = __( 'Integration with <a href="https://kiskadi.com/">Kiskadi CRM</a>.', 'woocommerce-integration-demo' );
		$this->form_fields        = $this->form_fields();

		$this->init_form_fields();
		$this->init_settings();

		add_action( 'woocommerce_update_options_integration_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	/** @return array<string,array<string,mixed>> */
	private function form_fields() : array {

        $gateways = WC()->payment_gateways->get_available_payment_gateways();
        $active_gateways = [];
        foreach($gateways as $id => $gateway) {
            $active_gateways[$id] = $gateway->get_title();
        }

		return array(
			'user'            => array(
				'title'       => __( 'User' ),
				'type'        => 'text',
				'description' => __( 'Enter with your user login from kiskadi', 'kiskadi' ),
				'desc_tip'    => true,
				'default'     => '',
			),
			'password'        => array(
				'title'       => __( 'Password' ),
				'type'        => 'password',
				'description' => __( 'Enter with your password login from kiskadi. You can find this in "User Profile" drop-down (top right corner) > API Keys.', 'kiskadi' ),
				'desc_tip'    => true,
				'default'     => '',
			),
            'enable_cashback' => array(
                'title'       => __( 'Cashback', 'kiskadi' ),
                'type'        => 'checkbox',
                'label'       => __( 'Enable cashback at checkout page', 'kiskadi' ),
                'default'     => 'yes',
                'description' => __( 'Enable the cashback option for customers at checkout', 'kiskadi' ),
            ),
            'enable_transactions' => array(
                'title'       => __( 'Transactions', 'kiskadi' ),
                'type'        => 'checkbox',
                'label'       => __( 'Enable the transactions sending to Kiskadi', 'kiskadi' ),
                'default'     => 'yes',
                'description' => __( 'Enable the option to send transaction to Kiskadi', 'kiskadi' ),
            ),
            'enable_transaction_by_payment_methods' => array(
                'title'       => __( 'Transactions by Payment Methods', 'kiskadi' ),
                'type'        => 'multiselect',
                'description' => __( 'Choose the Payment Methods.', 'kiskadi' ),
                'default'     => array_keys($active_gateways),
                'desc_tip'    => true,
                'options'     => $active_gateways
            ),
			'debug'           => array(
				'title'       => __( 'Debug Log' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable logging', 'kiskadi' ),
				'default'     => 'no',
				'description' => __( 'Log events such as API requests', 'kiskadi' ),
			),
		);
	}

	public function process_admin_options() : bool {
		$option_saved = parent::process_admin_options();

		try {
			$branch_data = ( new Branch_Client() )->first();
			( new Branch_Repository() )->update( $branch_data );
		} catch ( Exception $e ) {
			$message = $e->getMessage();

			WC_Admin_Settings::add_error( $message );
		}

		return $option_saved;
	}

	public function is_cashback_enable() : bool {
		if ( 'no' === $this->get_option( 'enable_cashback' ) ) {
			return false;
		}

		return true;
	}

	public function is_transactions_enable() : bool {
		if ( 'no' === $this->get_option( 'enable_transactions' ) ) {
			return false;
		}

		return true;
	}

	public function payment_methods_enable() : array {
		return $this->get_option( 'enable_transaction_by_payment_methods' );
	}
}
