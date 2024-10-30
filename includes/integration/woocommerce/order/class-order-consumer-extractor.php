<?php

namespace Kiskadi\Integration\WooCommerce\Order;

use Kiskadi\Consumer\Data\Address_Data;
use Kiskadi\Consumer\Data\Consumer_Data;
use Kiskadi\Consumer\Data\Person_Data;
use WC_Order;

class Order_Consumer_Extractor {

	/** @var WC_Order */
	private $order;

	public function __construct( WC_Order $order ) {
		$this->order = $order;
	}

	public function extract() : Consumer_Data {

		$person_data  = $this->person();
		$address_data = $this->address();

		$customer_id = intval( $this->order->get_customer_id() );

		$first_name = $this->order_billing_data( 'first_name' );
		$last_name  = $this->order_billing_data( 'last_name' );
		$name       = "{$first_name} {$last_name}";

		$consumer_data = new Consumer_Data( $customer_id, $name, $person_data, $address_data );
		return $consumer_data;
	}

	private function person() : Person_Data {
		$email     = $this->order_billing_data( 'email' );
//		$cpf       = $this->order_billing_data( 'cpf' );
//		$phone     = $this->order_billing_data( 'phone' );
//		$cellphone = $this->order_billing_data( 'cellphone' );

		$person_data = new Person_Data(
			$email,
			$cpf,
			$phone,
			$cellphone
		);

		return $person_data;
	}

	private function address() : Address_Data {
		$postcode     = $this->order_billing_data( 'postcode' );
		$address_1    = $this->order_billing_data( 'address_1' );
		$number       = intval( $this->order_billing_data( 'number' ) );
		$address_2    = $this->order_billing_data( 'address_2' );
		$neighborhood = $this->order_billing_data( 'neighborhood' );
		$city         = $this->order_billing_data( 'city' );
		$state        = $this->order_billing_data( 'state' );

		$address_data = new Address_Data(
			$postcode,
			$address_1,
			$number,
			$address_2,
			$neighborhood,
			$city,
			$state
		);

		return $address_data;
	}

	private function order_billing_data( string $key ) : string {
		$meta  = new Order_Meta( $this->order, "_billing_{$key}" );
		$value = $meta->value_as_string();

		return $value;
	}
}
