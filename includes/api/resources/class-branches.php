<?php
/**
 * API '/banches' resource.
 */

namespace Kiskadi\Api\Resources;

use Kiskadi\Api\Client\Client;
use Kiskadi\Api\Client\Collection_Client;

/**
 * API '/banches' resource.
 */
class Branches {
	/**
	 * Resource path.
	 *
	 * @var string
	 */
	const PATH = '/branches/';

	public function find( $id ) {
		$client = new Client();
		return $client->get( self::PATH . $id );
	}

	public function all( $data = array() ) {
		$client = new Collection_Client();
		return $client->get( self::PATH, $data );
	}
}
