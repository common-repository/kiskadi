<?php

namespace Kiskadi\Api\Response;

use Exception;

class Error_Response extends Response {

	public function error() : string {
		$object = $this->get_object();
		if ( false === isset( $object->errors ) ) {
			throw new Exception( __( 'Invalid error object.', 'kiskadi' ) );
		}
		return $object->errors;
	}

	protected function is_valid_status() : bool {
		return 404 === $this->status;
	}
}
