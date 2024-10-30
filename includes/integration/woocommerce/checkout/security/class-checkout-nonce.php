<?php

namespace Kiskadi\Integration\WooCommerce\Checkout\Security;

class Checkout_Nonce {

	/** @var string */
	private $action = 'kiskadi_checkout';

	public function create() : string {
		$nonce = wp_create_nonce( $this->action );

		return $nonce;
	}

	public function action() : string {
		return $this->action;
	}
}
