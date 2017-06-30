<?php
/**
 * Classe de validação de cadastro de doação
 * - Criada em 20/11/203
 * 
 * @author Niury Martins Pereira
 */

include_once('class.ValidaCad.php');

class ValidaCadCliente extends ValidaCad {
	
	public $cdregiao;
	public $cdbairro;
	public $dsregiao;
	public $obsregiao;
	
	public function __construct($post) {
		
		array_walk_recursive($post, 'array_insert_db');
		extract($post);
		
		$this->cdregiao = (int)$cdregiao;
		$this->cdbairro = $cdbairro;
		$this->dsregiao = $dsregiao;
		$this->obsregiao = $obsregiao;
	}
	
	public function Valida() {
		
		if (!$this->dsregiao) {
			$this->setMsg('Favor informar a descrição');	
		}
		
		if (!count($this->cdbairro)) {
			$this->setMsg('Favor informar os bairros');
		}
		
		if ($this->getMsg()) {
			$this->resultado = false;
		}
		else {
			$this->resultado = true;
		}
	}
}