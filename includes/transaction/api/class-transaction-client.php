<?php

namespace Kiskadi\Transaction\Api;

use Exception;
use Kiskadi\Api\Client\Client;
use Kiskadi\Api\Response\Error_Response;
use Kiskadi\Consumer\Data\Consumer_Api_Data;
use Kiskadi\Transaction\Data\Transaction_Api_Data;
use Kiskadi\Transaction\Data\Transaction_Data;
use Kiskadi\Transaction\Data\Transaction_Exchange_Data;
use Kiskadi\Transaction\Transformer\Transaction_Api_Transformer;
use Kiskadi\Transaction\Transformer\Transaction_Exchange_Api_Transformer;

class Transaction_Client extends Client {
	/** @var string */
	private $path = '/transactions/';

	public function create_point( Transaction_Data $transaction ) : Transaction_Api_Data {
		$data     = ( new Transaction_Api_Transformer( $transaction ) )->transform();
		$response = $this->post( $this->path . 'point', $data );
		if ( $response instanceof Error_Response ) {
			$error_message = $response->error();
			throw new Exception( $error_message );
		}

		$transation_object = $response->get_object();

		$id          = intval( $transation_object->id );
		$points      = floatval( $transation_object->points );
		$consumer_id = intval( $transation_object->consumer_id );

		$consumer    = new Consumer_Api_Data( $consumer_id );
		$transaction = new Transaction_Api_Data( $id, $points, $consumer );

		return $transaction;
	}

	public function create_exchange( Transaction_Exchange_Data $transaction ) : Transaction_Api_Data {
		$data     = ( new Transaction_Exchange_Api_Transformer( $transaction ) )->transform();
		$response = $this->post( $this->path . 'exchange', $data );
		if ( $response instanceof Error_Response ) {
			$error_message = $response->error();
			throw new Exception( $error_message );
		}

		$transation_object = $response->get_object();

		$id          = intval( $transation_object->id );
		$points      = floatval( $transation_object->points );
		$consumer_id = intval( $transation_object->consumer_id );

		$consumer    = new Consumer_Api_Data( $consumer_id );
		$transaction = new Transaction_Api_Data( $id, $points, $consumer );

		return $transaction;
	}
}
