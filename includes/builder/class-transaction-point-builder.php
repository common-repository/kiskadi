<?php

namespace Kiskadi\Builder;

use WC_Order;

class Transaction_Point_Builder {

	private $order;
	private $branch_id;

	public function __construct( WC_Order $order, int $branch_id ) {
		$this->order     = $order;
		$this->branch_id = $branch_id;
	}

	public function build() : array {

		$data = array();

		$data = array(
			'consumer'    => $this->build_consumer(),
			'spent_value' => floatval( $this->order->get_total() ),
			'branch_id'   => $this->branch_id,
			'products'    => $this->build_products(),
		);

		return $data;
	}

	private function build_consumer() : array {
		$cpf               = '/\.|-|\//';
		$phone_clean_regex = '/\(|\)|\s|-/';

		$customer_id = intval( $this->order->get_customer_id() );

		$customer = array(
			'name'               => $this->order->get_meta( '_billing_first_name' ) . ' ' . $this->order->get_meta( '_billing_last_name' ),
			'phone_number'       => preg_replace( $phone_clean_regex, '', $this->order->get_meta( '_billing_phone' ) ),
			'email'              => $this->order->get_meta( '_billing_email' ),
			'cpf'                => preg_replace( $cpf, '', $this->order->get_meta( '_billing_cpf' ) ),
			'city'               => $this->order->get_meta( '_billing_city' ),
			'uf'                 => $this->order->get_meta( '_billing_state' ),
			'address'            => $this->order->get_meta( '_billing_address_1' ),
			'number'             => $this->order->get_meta( '_billing_number' ),
			'cep'                => $this->order->get_meta( '_billing_postcode' ),
			'complement'         => $this->order->get_meta( '_billing_address_2' ),
			'neighborhood'       => $this->order->get_meta( '_billing_neighborhood' ),
			'other_phone_number' => preg_replace( $phone_clean_regex, '', $this->order->get_meta( '_billing_cellphone' ) ),
		);

		if ( 0 !== $customer_id ) {
			$customer['external_id'] = $customer_id;
		}

		return $customer;
	}

	private function build_products() : array {
		$products = array();

		foreach ( $this->order->get_items() as $item ) {
			$product        = $item->get_product();
			$category_names = array();

			$categories = get_the_terms( $product->get_id(), 'product_cat' );

			foreach ( $categories as $category ) {
				$category_names[] = $category->name;
			}

			$products[] = array(
				'code'           => $product->get_id(),
				'description'    => $item->get_name(),
				'quantity'       => intval( $item->get_quantity() ),
				'url_image'      => get_the_post_thumbnail_url( $product->get_id() ),
				'spent_value'    => floatval( $item->get_total() ),
				'category_names' => '',
			);
		}

		return $products;
	}
}
