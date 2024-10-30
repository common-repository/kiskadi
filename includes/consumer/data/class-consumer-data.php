<?php

namespace Kiskadi\Consumer\Data;

class Consumer_Data {

	/** @var int */
	private $external_id;

	/** @var string */
	private $name;

	/** @var Person_Data */
	private $person;

	/** @var Address_Data */
	private $address;

	public function __construct( int $external_id, string $name, Person_Data $person, Address_Data $address ) {
		$this->external_id = $external_id;
		$this->name        = $name;
		$this->person      = $person;
		$this->address     = $address;
	}

	public function external_id() : int {
		return $this->external_id;
	}

	public function name() : string {
		return $this->name;
	}

	public function person() : Person_Data {
		return $this->person;
	}

	public function address() : Address_Data {
		return $this->address;
	}
}
