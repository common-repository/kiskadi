<?php
/**
 * API '/vendors' resource.
 */

namespace Kiskadi\Api\Resources;

use Kiskadi\Api\Client\Client;
use Kiskadi\Api\Client\Collection_Client;

/**
 * API '/vendors' resource.
 */
class Vendors {
	/**
	 * Resource path.
	 *
	 * @var string
	 */
	const PATH = '/vendors/';

	public function find( $id ) {
		$client = new Client();
		return $client->get( self::PATH . $id );
	}

	public function all() {
		$client = new Collection_Client();
		return $client->get( self::PATH );
	}
}
