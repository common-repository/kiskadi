<?php

namespace Kiskadi\Integration\WooCommerce\Data\Transformer;

use Kiskadi\Integration\WooCommerce\Data\Product_Data;
use Kiskadi\Integration\WooCommerce\Data\Transformer\Contract\Product_Contract_Transformer;

class Product_Array_Transformer implements Product_Contract_Transformer {

	/** @var Product_Data */
	private $product;

	public function __construct( Product_Data $product ) {
		$this->product = $product;
	}

	/** @return Product */
	public function transform() : array {
		return array(
			'code'           => $this->product->code(),
			'description'    => $this->product->description(),
			'quantity'       => $this->product->quantity(),
			'url_image'      => $this->product->url_image(),
			'spent_value'    => $this->product->spent_value(),
			'category_names' => $this->product->category_names(),
		);
	}

}
