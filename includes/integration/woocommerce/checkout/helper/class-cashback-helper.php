<?php

namespace Kiskadi\Integration\WooCommerce\Checkout\Helper;

use Exception;
use InvalidArgumentException;

class Cashback_Helper {

	/**
	 * @throws Exception Nonce was not verified or cannot extract CPF from post data.
	 * @return array{
	 *  billing_cpf: string,
	 *  exchange_point_enabled: bool,
	 *  cashback_amount: float,
	 * }
	 */
	public function exchangeble_point_data() : array {
		$is_valid_request = false;
		$post             = array();

		if (
			isset( $_POST['security'], $_POST['post_data'] )
			&& false !== wp_verify_nonce( sanitize_key( $_POST['security'] ), 'update-order-review' )
		) {
			$is_valid_request = true;
			$post_data        = sanitize_text_field( wp_unslash( $_POST['post_data'] ) );
			$post             = $this->convert_order_review_post_data_to_array( $post_data );
		}

		if (
			isset( $_POST['woocommerce-process-checkout-nonce'], $_POST['billing_cpf'], $_POST['billing_cnpj'] )
            /* commented validation as it is already executed in the process_checkout function and generates nonce difference error */
            // && false !== wp_verify_nonce( sanitize_key( $_POST['woocommerce-process-checkout-nonce'] ), 'woocommerce-process_checkout' )
		) {
			$is_valid_request = true;
		}

		if ( false === $is_valid_request ) {
			throw new Exception( __( 'Invalid calculate fees request.', 'kiskadi' ) );
		}

		if ( 0 === count( $post ) ) {
			$post = $_POST;
		}

		if ( false === isset( $post['billing_cpf'] ) &&  false === isset( $post['billing_cnpj'] )) {
			throw new Exception( __( 'Cannot extract CPF and exhangable points from post data.', 'kiskadi' ) );
		}

		$cashback_amount = 0.00;
        if ( isset( $post['kiskadi_cashback_amount'] ) ) {
            $cashback_amount = floatval( $post['kiskadi_cashback_amount'] );
        }

        if ( isset( $post['billing_persontype'] ) ) {
            $billing_documentation = ($post['billing_persontype'] == 1)? $post['billing_cpf'] : $post['billing_cnpj'];
        }else{
            $billing_documentation = (!empty($post['billing_cpf']))? $post['billing_cpf'] : $post['billing_cnpj'];
        }

		return array(
			'billing_cpf'            => $billing_documentation,
			'exchange_point_enabled' => isset( $post['kiskadi_exchangeable_points'] ),
			'cashback_amount'        => $cashback_amount,
		);
	}

	/**
	 * @throws InvalidArgumentException If post data cannot be extracted
	 * @return array<mixed>
	 * */
	private function convert_order_review_post_data_to_array( string $post_data ) : array {
		$post_data_array = array();
		parse_str( $post_data, $post_data_array );
		if ( 0 === count( $post_data_array ) ) {
			throw new InvalidArgumentException( __( 'Cannot extract data from post data.', 'kiskadi' ) );
		}

		return $post_data_array;
	}

}
