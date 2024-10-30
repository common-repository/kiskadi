<?php

namespace Kiskadi\Transaction\Data;

use Kiskadi\Consumer\Data\Consumer_Data;
use Kiskadi\Integration\WooCommerce\Data\Product_Data;
use Kiskadi\Vendor\Data\Branch_Data;

class Transaction_Data {

	/** @var Consumer_Data */
	private $consumer;

	/** @var float */
	private $spent_value;

	/** @var Branch_Data */
	private $branch;

	/** @var Product_Data[] */
	private $products;

	/** @param Product_Data[] $products */
	public function __construct( Consumer_Data $consumer, float $spent_value, Branch_Data $branch, $products ) {
		$this->consumer    = $consumer;
		$this->spent_value = $spent_value;
		$this->branch      = $branch;
		$this->products    = $products;
	}

	public function consumer(): Consumer_Data {
		return $this->consumer;
	}

	public function spent_value(): float {
		return $this->spent_value;
	}

	public function branch(): Branch_Data {
		return $this->branch;
	}

	/** @return Product_Data[] */
	public function products() {
		return $this->products;
	}
}
