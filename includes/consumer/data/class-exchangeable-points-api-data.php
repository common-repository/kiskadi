<?php

namespace Kiskadi\Consumer\Data;

class Exchangeable_Points_Api_Data {

	/** @var float */
	private $available_discount;

	/** @var float */
	private $points_to_exchange;

	public function __construct( float $available_discount, float $points_to_exchange ) {
		$this->available_discount = $available_discount;
		$this->points_to_exchange = $points_to_exchange;
	}

	public function available_discount() : float {
		return $this->available_discount;
	}

	public function points_to_exchange() : float {
		return $this->points_to_exchange;
	}
}
