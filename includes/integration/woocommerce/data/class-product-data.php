<?php

namespace Kiskadi\Integration\WooCommerce\Data;

use WC_Order_Item_Product;

class Product_Data {

	/** @var WC_Order_Item_Product */
	private $product_item;

	public function __construct( WC_Order_Item_Product $product_item ) {
		$this->product_item = $product_item;
	}

	public function code() : int {
		return $this->product_item->get_product_id();
	}

	public function description() : string {
		$description = $this->product_item->get_name();
		return $description;
	}

	public function quantity() : int {
		$quantity = intval( $this->product_item->get_quantity() );
		return $quantity;
	}

	public function url_image() : string {
		$product_image = get_the_post_thumbnail_url( $this->product_item->get_product_id() );

		if ( false === $product_image ) {
			$product_image = '';
		}

		return $product_image;
	}

	public function spent_value() : float {
		$spent_value = floatval( $this->product_item->get_total() );
		return $spent_value;
	}

	/** @return array<int, string> */
	public function category_names() {
		$category_names = array();
		$categories     = get_the_terms( $this->product_item->get_product_id(), 'product_cat' );

		if ( false === is_iterable( $categories ) ) {
			return $category_names;
		}

		foreach ( $categories as $category ) {
			$category_names[] = $category->name;
		}

		return $category_names;
	}

}

