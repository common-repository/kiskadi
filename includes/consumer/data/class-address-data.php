<?php

namespace Kiskadi\Consumer\Data;

class Address_Data {

	/** @var string */
	private $cep;

	/** @var string */
	private $address;

	/** @var int */
	private $number;

	/** @var string */
	private $complement;

	/** @var string */
	private $neighborhood;

	/** @var string */
	private $city;

	/** @var string */
	private $uf;

	public function __construct( string $cep, string $address, int $number, string $complement, string $neighborhood, string $city, string $uf ) {
		$this->cep          = $cep;
		$this->address      = $address;
		$this->number       = $number;
		$this->complement   = $complement;
		$this->neighborhood = $neighborhood;
		$this->city         = $city;
		$this->uf           = $uf;
	}

	public function cep() : string {
		return $this->cep;
	}

	public function address() : string {
		return $this->address;
	}

	public function number() : int {
		return $this->number;
	}

	public function complement() : string {
		return $this->complement;
	}

	public function neighborhood() : string {
		return $this->neighborhood;
	}

	public function city() : string {
		return $this->city;
	}

	public function uf() : string {
		return $this->uf;
	}

}

