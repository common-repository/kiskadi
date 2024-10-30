<?php
/**
 * API '/Transactions' resource.
 */

namespace Kiskadi\Api\Resources;

use Kiskadi\Api\Client\Client;
/**
 * API '/Transactions' resource.
 */
class Transactions {
	/**
	 * Resource path.
	 *
	 * @var string
	 */
	const PATH = '/transactions/';

	public function find( $id ) {
		$client = new Client();
		return $client->get( self::PATH . $id );
	}

	public function create( $data ) {
		$client = new Client();
		return $client->post( self::PATH, wp_json_encode( $data ) );
	}

	public function create_point( $data ) {
		$client = new Client();
		return $client->post( self::PATH . 'point', wp_json_encode( $data ) );
	}

}
