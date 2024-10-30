<?php

namespace Kiskadi\Api\Auth;

use Kiskadi\Api\Auth\Auth;
use Kiskadi\Integration\WooCommerce\Admin\Kiskadi_Integration_Admin;

class Admin_Auth extends Auth {

	public function __construct( Kiskadi_Integration_Admin $settings ) {
		$this->user     = $settings->get_option( 'user' );
		$this->password = $settings->get_option( 'password' );
	}
}
