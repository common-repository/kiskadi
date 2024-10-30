<?php

namespace Kiskadi\Transaction\Transformer;

use Exception;
use Kiskadi\Consumer\Data\Transformer\Consumer_Array_Transformer;
use Kiskadi\Integration\WooCommerce\Data\Transformer\Product_Array_Transformer;
use Kiskadi\Transaction\Data\Transaction_Exchange_Data;

class Transaction_Exchange_Api_Transformer {

	/** @var Transaction_Exchange_Data */
	private $transaction_data;

	public function __construct( Transaction_Exchange_Data $transaction_data ) {
		$this->transaction_data = $transaction_data;
	}

	/**
	 * @throws Exception
	 * @return Transaction_Exchange
	 * */
	public function transform() {
		$consumer       = $this->transaction_data->consumer();
		$consumer_array = ( new Consumer_Array_Transformer( $consumer ) )->transform();

		$products      = $this->transaction_data->products();
		$product_array = array();
		foreach ( $products as $product ) {
			$product_array[] = ( new Product_Array_Transformer( $product ) )->transform();
		}

		$branch = $this->transaction_data->branch();

		$cpf = preg_replace( '/\.|-|\//', '', $consumer_array['cpf'] );
		if ( null === $cpf ) {
			throw new Exception( __( 'Cannot sanitize CPF.', 'kiskadi' ) );
		}

		return array(
			'consumer'           => array( 'cpf' => $consumer_array['cpf'] ),
			'order_value'        => $this->transaction_data->spent_value(),
			'points_to_exchange' => number_format($this->transaction_data->points_to_exchange(), 2, '.', ''),
			'branch_id'          => $branch->id(),
			'products'           => $product_array,
		);
	}
}
