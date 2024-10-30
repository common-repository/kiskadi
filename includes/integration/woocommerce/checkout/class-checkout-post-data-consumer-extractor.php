<?php

namespace Kiskadi\Integration\WooCommerce\Checkout;

use Kiskadi\Consumer\Data\Address_Data;
use Kiskadi\Consumer\Data\Consumer_Data;
use Kiskadi\Consumer\Data\Person_Data;

class Checkout_Post_Data_Consumer_Extractor {

	/** @var array<mixed> */
	private $post_data;

	/** @param array<mixed> $post_data */
	public function __construct( array $post_data ) {
		$this->post_data = $post_data;
	}

	public function extract() : Consumer_Data {
		$person_data  = $this->person();
		$address_data = $this->address();

		$customer_id = get_current_user_id();

		$first_name = $this->checkout_billing_post_data( 'first_name' );
		$last_name  = $this->checkout_billing_post_data( 'last_name' );
		$name       = "{$first_name} {$last_name}";

		$consumer_data = new Consumer_Data( $customer_id, $name, $person_data, $address_data );
		return $consumer_data;
	}

	private function person() : Person_Data {
		$email     = $this->checkout_billing_post_data( 'email' );
		$cpf       = $this->checkout_billing_post_data( 'cpf' );
		$phone     = $this->checkout_billing_post_data( 'phone' );
		$cellphone = $this->checkout_billing_post_data( 'cellphone' );

		$person_data = new Person_Data(
			$email,
			$cpf,
			$phone,
			$cellphone
		);

		return $person_data;
	}

	private function address() : Address_Data {
		$postcode     = $this->checkout_billing_post_data( 'postcode' );
		$address_1    = $this->checkout_billing_post_data( 'address_1' );
		$number       = intval( $this->checkout_billing_post_data( 'number' ) );
		$address_2    = $this->checkout_billing_post_data( 'address_2' );
		$neighborhood = $this->checkout_billing_post_data( 'neighborhood' );
		$city         = $this->checkout_billing_post_data( 'city' );
		$state        = $this->checkout_billing_post_data( 'state' );

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

	private function checkout_billing_post_data( string $key ) : string {
		$value = strval( $this->post_data[ "billing_{$key}" ] );

		return $value;
	}
}
