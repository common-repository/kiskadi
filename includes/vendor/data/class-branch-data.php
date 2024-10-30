<?php

namespace Kiskadi\Vendor\Data;

class Branch_Data {

	/** @var int */
	private $id;

	/** @var string */
	private $cnpj;

	public function __construct( int $id, string $cnpj ) {
		$this->id   = $id;
		$this->cnpj = $cnpj;
	}

	public function id() : int {
		return $this->id;
	}

	public function cnpj() : string {
		return $this->cnpj;
	}

}

