<?php

namespace Kiskadi\Api\Response;

use Exception;
use stdClass;

class Response {

	/** @var string */
	protected $data;

	/** @var int */
	protected $status;

	public function __construct( int $status, string $data ) {
		$this->status = $status;
		if ( false === $this->is_valid_status() ) {
            preg_match_all('/\[(.*?)\]/', $data, $matches);
            $errorMessage = !empty($matches[1][0])? $matches[1][0] : 'Request to Kiskadi API returned an unexpected status code.';
			throw new Exception( __( $errorMessage, 'kiskadi' ) );
		}
		$this->data = $data;
	}

	public function get_raw() : string {
		return $this->data;
	}

	public function get_object() : stdClass {
		$json = (object) json_decode( $this->data, false );
		return $json;
	}

	protected function is_valid_status() : bool {
		return 300 > $this->status && 200 <= $this->status;
	}
}
