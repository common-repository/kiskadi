<?php

namespace Kiskadi\Consumer\Data;

class Consumer_Api_Data {

	/** @var int */
	private $id;

	public function __construct( int $id ) {
		$this->id = $id;
	}

	public function id() : int {
		return $this->id;
	}
}
