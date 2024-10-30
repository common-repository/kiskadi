<?php

namespace Kiskadi\Vendor\Repository;

use Kiskadi\Vendor\Data\Branch_Data;

class Branch_Repository {

	/** @var string */
	private $id_option_key = 'kiskadi_branch_id';

	/** @var string */
	private $cnpj_option_key = 'kiskadi_branch_cnpj';

	public function get_branch() : ?Branch_Data {
		$branch_id = get_option( $this->id_option_key, 0 );
		if ( 0 === $branch_id ) {
			return null;
		}

		$id = intval( $branch_id );

		$branch_cnpj = get_option( $this->cnpj_option_key, '' );
		$cnpj        = strval( $branch_cnpj );

		return new Branch_Data( $id, $cnpj );
	}

	public function update( Branch_Data $branch_data ) : void {
		update_option( $this->id_option_key, $branch_data->id(), 'yes' );
		update_option( $this->cnpj_option_key, $branch_data->cnpj(), 'yes' );
	}
}
