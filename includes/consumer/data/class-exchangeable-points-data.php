<?php

namespace Kiskadi\Consumer\Data;

use Kiskadi\Vendor\Data\Branch_Data;

class Exchangeable_Points_Data {

	/** @var string */
	private $branch_cnpj;

	/** @var float */
	private $order_value;

	/** @var string */
	private $cpf;

	public function __construct( Branch_Data $branch, float $order_value, string $cpf ) {
		$this->branch_cnpj = $branch->cnpj();
		$this->order_value = $order_value;
		$this->cpf         = $cpf;
	}

	public function branch_cnpj() : string {
		return $this->branch_cnpj;
	}

	public function order_value() : float {
		return $this->order_value;
	}

	public function cpf() : string {
		return $this->cpf;
	}
}
