<?php

namespace Kiskadi\Consumer\Data\Transformer;

use Kiskadi\Consumer\Data\Consumer_Data;
use Kiskadi\Consumer\Data\Transformer\Contract\Consumer_Contract_Transformer;

class Consumer_Array_Transformer implements Consumer_Contract_Transformer {

	/** @var Consumer_Data */
	private $consumer;

	public function __construct( Consumer_Data $consumer ) {
		$this->consumer = $consumer;
	}

	/** @return Consumer */
	public function transform() : array {
		$person  = $this->consumer->person();
		$address = $this->consumer->address();

		return array(
			'external_id'        => $this->consumer->external_id(),
			'name'               => $this->consumer->name(),
			'email'              => $person->email(),
			'cpf'                => $person->cpf(),
			'phone_number'       => $person->phone_number(),
			'other_phone_number' => $person->other_phone_number(),
			'cep'                => $address->cep(),
			'address'            => $address->address(),
			'number'             => $address->number(),
			'complement'         => $address->complement(),
			'neighborhood'       => $address->neighborhood(),
			'city'               => $address->city(),
			'uf'                 => $address->uf(),
		);
	}

}
