<?php

namespace Kiskadi\Transaction\Data;

use Kiskadi\Consumer\Data\Consumer_Api_Data;

class Transaction_Api_Data {

	/** @var int */
	private $id;

	/** @var float */
	private $points;

	/** @var Consumer_Api_Data */
	private $consumer;

	public function __construct( int $id, float $points, Consumer_Api_Data $consumer ) {
		$this->id       = $id;
		$this->points   = $points;
		$this->consumer = $consumer;
	}

	public function id(): int {
		return $this->id;
	}

	public function points(): float {
		return $this->points;
	}

	public function consumer(): Consumer_Api_Data {
		return $this->consumer;
	}
}
