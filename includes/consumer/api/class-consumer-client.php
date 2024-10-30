<?php

namespace Kiskadi\Consumer\Api;

use Exception;
use Kiskadi\Api\Client\Client;
use Kiskadi\Api\Response\Error_Response;
use Kiskadi\Consumer\Data\Exchangeable_Points_Api_Data;
use Kiskadi\Consumer\Data\Exchangeable_Points_Data;
use Kiskadi\Consumer\Data\Transformer\Exchangeable_Points_Array_Transformer;

class Consumer_Client extends Client {

	/** @var string */
	private $path = '/consumers/';

	public function exchangeable_points( Exchangeable_Points_Data $consumer ) : Exchangeable_Points_Api_Data {
		$data     = ( new Exchangeable_Points_Array_Transformer( $consumer ) )->transform();
		$response = $this->get( $this->path . 'exchangeable_points', $data );
		if ( $response instanceof Error_Response ) {
			$error_message = $response->error();
			throw new Exception( $error_message );
		}

		$exchangeable_points_object = $response->get_object();

		$available_discount  = floatval( $exchangeable_points_object->available_discount );
		$points_to_exchange  = floatval( $exchangeable_points_object->points_to_exchange );
		$exchangeable_points = new Exchangeable_Points_Api_Data( $available_discount, $points_to_exchange );

		return $exchangeable_points;
	}
}
