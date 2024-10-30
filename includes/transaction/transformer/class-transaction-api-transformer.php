<?php

namespace Kiskadi\Transaction\Transformer;

use Exception;
use Kiskadi\Consumer\Data\Transformer\Consumer_Array_Transformer;
use Kiskadi\Integration\WooCommerce\Data\Transformer\Product_Array_Transformer;
use Kiskadi\Transaction\Data\Transaction_Data;

class Transaction_Api_Transformer {

	/** @var Transaction_Data */
	private $transaction_data;

	public function __construct( Transaction_Data $transaction_data ) {
		$this->transaction_data = $transaction_data;
	}

	/** @return Transaction */
	public function transform() : array {
		$consumer       = $this->transaction_data->consumer();
		$consumer_array = ( new Consumer_Array_Transformer( $consumer ) )->transform();
		$consumer_array = $this->sanitize_consumer( $consumer_array );

		$products      = $this->transaction_data->products();
		$product_array = array();
		foreach ( $products as $product ) {
			$product_array[] = ( new Product_Array_Transformer( $product ) )->transform();
		}

		$branch = $this->transaction_data->branch();

		return array(
			'consumer'    => $consumer_array,
			'spent_value' => $this->transaction_data->spent_value(),
			'branch_id'   => $branch->id(),
			'products'    => $product_array,
		);
	}

	/**
	 * @param Consumer $consumer_array
	 * @return Consumer
	 */
	private function sanitize_consumer( array $consumer_array ) : array {
		if ( isset( $consumer_array['external_id'] ) && 0 === $consumer_array['external_id'] ) {
			unset( $consumer_array['external_id'] );
		}

		$consumer_array['cpf']                = $this->regex_sanitize( 'cpf', $consumer_array['cpf'], '/\.|-|\//' );
		$consumer_array['phone_number']       = $this->regex_sanitize( 'phone_number', $consumer_array['phone_number'], '/\(|\)|\s|-/' );
		$consumer_array['other_phone_number'] = $this->regex_sanitize( 'other_phone_number', $consumer_array['other_phone_number'], '/\(|\)|\s|-/' );

		return $consumer_array;
	}

	private function regex_sanitize( string $key, string $value, string $regex ) : string {
		$value = preg_replace( $regex, '', $value );
		if ( null === $value ) {
			/* translators: The consumer property name */
			throw new Exception( sprintf( __( 'Cannot sanitize %s.', 'kiskadi' ), $key ) );
		}

		return $value;
	}
}
