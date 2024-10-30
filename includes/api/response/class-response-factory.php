<?php

namespace Kiskadi\Api\Response;

use Exception;
use Kiskadi\Integration\WooCommerce\Log\Logger;

class Response_Factory {

	public function create( int $status, string $data ) : Response {
		try {
			$response = new Response( $status, $data );
		} catch ( Exception $e ) {
			$response = new Error_Response( $status, $data );
		}

		return $response;
	}
}
