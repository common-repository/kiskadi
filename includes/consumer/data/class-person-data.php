<?php

namespace Kiskadi\Consumer\Data;

class Person_Data {

	/** @var string */
	private $email;

	/** @var string */
	private $cpf;

	/** @var string */
	private $phone_number;

	/** @var string */
	private $other_phone_number;

	public function __construct( string $email, string $cpf, string $phone_number, string $other_phone_number ) {
		$this->email              = $email;
		$this->cpf                = $cpf;
		$this->phone_number       = $phone_number;
		$this->other_phone_number = $other_phone_number;
	}

	public function email() : string {
		return $this->email;
	}

	public function cpf() : string {
		return $this->cpf;
	}

	public function phone_number() : string {
		return $this->phone_number;
	}

	public function other_phone_number() : string {
		return $this->other_phone_number;
	}

}

