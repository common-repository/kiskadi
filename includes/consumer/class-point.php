<?php

namespace Kiskadi\Consumer;

use Exception;
use Kiskadi\Consumer\Api\Consumer_Client;
use Kiskadi\Consumer\Data\Exchangeable_Points_Api_Data;
use Kiskadi\Consumer\Data\Exchangeable_Points_Data;
use Kiskadi\Vendor\Repository\Branch_Repository;

class Point {

	public function query_exchangeable( float $order_value, string $cpf ) : Exchangeable_Points_Api_Data {
		$branch_data = ( new Branch_Repository() )->get_branch();

		if ( null === $branch_data ) {
			throw new Exception( __( 'Cannot load branch.', 'kiskadi' ) );
		}

		$exchangeable_points_data     = new Exchangeable_Points_Data( $branch_data, $order_value, $cpf );
		$exchangeable_points_api_data = ( new Consumer_Client() )->exchangeable_points( $exchangeable_points_data );

		return $exchangeable_points_api_data;
	}
}
