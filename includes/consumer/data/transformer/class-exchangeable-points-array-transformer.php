<?php

namespace Kiskadi\Consumer\Data\Transformer;

use Exception;
use Kiskadi\Consumer\Data\Exchangeable_Points_Data;
use Kiskadi\Consumer\Data\Transformer\Contract\Exchangeable_Points_Contract_Transformer;

class Exchangeable_Points_Array_Transformer implements Exchangeable_Points_Contract_Transformer {

	/** @var Exchangeable_Points_Data */
	private $consumer;

	public function __construct( Exchangeable_Points_Data $consumer ) {
		$this->consumer = $consumer;
	}

	/**
	 * @throws Exception
	 * @return Consumer_Exchangeable_Points
	 */
	public function transform() : array {
		$cpf = preg_replace( '/\.|-|\//', '', $this->consumer->cpf() );
		if ( null === $cpf ) {
			throw new Exception( __( 'Cannot sanitize CPF.', 'kiskadi' ) );
		}

		return array(
			'branch_cnpj' => $this->consumer->branch_cnpj(),
			'order_value' => $this->consumer->order_value(),
			'cpf'         => $cpf,
		);
	}

}
