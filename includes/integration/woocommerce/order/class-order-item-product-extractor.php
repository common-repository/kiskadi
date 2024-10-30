<?php

namespace Kiskadi\Integration\WooCommerce\Order;

use Kiskadi\Integration\WooCommerce\Data\Product_Data;
use WC_Order;
use WC_Order_Item_Product;

class Order_Item_Product_Extractor {

	/** @var WC_Order */
	private $order;

	public function __construct( WC_Order $order ) {
		$this->order = $order;
	}

	/** @return Product_Data[] */
	public function extract() {
		$products = array();
		foreach ( $this->order->get_items() as $item ) {
			$order_tem_product = new WC_Order_Item_Product( $item->get_id() );
			$products[]        = new Product_Data( $order_tem_product );
		}

		return $products;
	}
}
