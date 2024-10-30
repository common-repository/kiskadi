<?php

namespace Kiskadi\Transaction\Data;

use Kiskadi\Consumer\Data\Consumer_Data;
use Kiskadi\Integration\WooCommerce\Data\Product_Data;
use Kiskadi\Vendor\Data\Branch_Data;

class Transaction_Exchange_Data extends Transaction_Data {

	/** @var float */
	private $points_to_exchange;

	/** @param Product_Data[] $products */
	public function __construct( Consumer_Data $consumer, float $points_to_exchange, float $spent_value, Branch_Data $branch, $products ) {
		parent::__construct( $consumer, $spent_value, $branch, $products );
		$this->points_to_exchange = $points_to_exchange;
	}

	public function points_to_exchange(): float {
		return $this->points_to_exchange;
	}
}
