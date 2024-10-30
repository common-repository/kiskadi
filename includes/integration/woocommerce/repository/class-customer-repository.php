<?php

namespace Kiskadi\Integration\WooCommerce\Repository;

use WP_User;

class Customer_Repository {

	/** @var string */
	private $kiskadi_consumer_id_key = 'kiskadi_consumer_id';

	public function update_kiskadi_consumer_id( WP_User $user, int $consumer_id ) : void {
		update_user_meta( $user->ID, $this->kiskadi_consumer_id_key, $consumer_id );
	}
}
