<?php
/**
 * Classe abstrata de validações de cadastro
 */
abstract class ValidaCad {
	protected $resultado;
	protected $msg;
	protected $con;

	public function setMsg($msg) {
		if ($this->msg != '') {
			$this->msg .= '<br />';
		}

		$this->msg .= '- ' . $msg;
	}

	public function getResultado() {
		return $this->resultado;
	}

	public function getMsg() {
		return $this->msg;
	}
	
	abstract protected function Valida();
}