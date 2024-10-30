<?php

namespace Kiskadi\Api\Auth;

abstract class Auth {

	/** @var string */
	protected $user;

	/** @var string */
	protected $password;

	public function authorization() : string {
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$hash = base64_encode( $this->user . ':' . $this->password );

		return 'Basic ' . $hash;
	}
}
