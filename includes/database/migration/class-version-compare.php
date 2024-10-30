<?php

namespace Kiskadi\Database\Migration;

class Version_Compare {

	public function is_out_of_boundary_version( string $version, string $from_version, string $to_version ) : bool {
		$lower_bondary_version  = $this->lower_version( $from_version, $to_version );
		$is_lower_than_boundary = version_compare( $version, $lower_bondary_version, '<' );
		if ( $is_lower_than_boundary ) {
			return true;
		}

		$higher_bondary_version  = $this->higher_version( $from_version, $to_version );
		$is_higher_than_boundary = version_compare( $version, $higher_bondary_version, '<' );
		if ( $is_higher_than_boundary ) {
			return true;
		}

		return false;
	}

	public function lower_version( string $from_version, string $to_version ) : string {
		if ( $this->is_upgrade( $from_version, $to_version ) ) {
			return $from_version;
		}

		return $to_version;
	}

	public function higher_version( string $from_version, string $to_version ) : string {
		if ( $this->is_upgrade( $from_version, $to_version ) ) {
			return $to_version;
		}

		return $from_version;
	}

	public function is_upgrade( string $from_version, string $to_version ) : bool {
		return version_compare( $from_version, $to_version, '<' );
	}

	public function is_downgrade( string $from_version, string $to_version ) : bool {
		return version_compare( $from_version, $to_version, '>' );
	}
}
