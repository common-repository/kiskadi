<?php

namespace Kiskadi\Vendor\Api;

use Exception;
use Kiskadi\Api\Client\Client;
use Kiskadi\Api\Response\Error_Response;
use Kiskadi\Vendor\Data\Branch_Data;

class Branch_Client extends Client {

	/** @var string */
	private $path = '/branches/';

	public function first() : Branch_Data {
		$response = $this->get( $this->path );
		if ( $response instanceof Error_Response ) {
			$error_message = $response->error();
			throw new Exception( $error_message );
		}

		$branch = $response->get_object();

		$first_branch = $branch->data[0];
		$branch_data  = new Branch_Data( $first_branch->id, $first_branch->cnpj );

		return $branch_data;
	}
}
