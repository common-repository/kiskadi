<?php

namespace Kiskadi\Repository;

use WP_User;

class Consumer_Repository {

	public function get_by_id( int $user_id ) : ?WP_User {
		$user = get_user_by( 'id', $user_id );

		if ( false === $user ) {
			return null;
		}

		return $user;
	}

	public function update_kiskadi_consumer_id( WP_User $user, int $kiskadi_consumer_id ) : void {
		update_user_meta( $user->ID, 'kiskadi_consumer_id', $kiskadi_consumer_id );
	}
}
