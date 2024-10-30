<?php

namespace Kiskadi\Integration\WooCommerce\Fee;

use WC_Cart;

class Cashback_Fee {

	/** @var string */
	private $kiskadi_cashback_id = '_kiskadi_cashback';

	/** @var float */
	private $discount_in_currency;

	public function __construct( float $discount_in_currency ) {
		$this->discount_in_currency = $discount_in_currency;
	}

	public function add_to_cart( WC_Cart $cart ) : void {
		$fee = array(
			'id'        => $this->kiskadi_cashback_id,
			'name'      => __( 'Kiskadi Cashback', 'kiskadi' ),
			'amount'    => -1 * $this->discount_in_currency,
			'taxable'   => false,
			'tax_class' => 'non-taxable',
		);

		$cart->fees_api()->add_fee( $fee );
	}
}
