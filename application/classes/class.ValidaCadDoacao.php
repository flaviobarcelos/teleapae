<?php
/**
 * Classe de validação de cadastro de doação
 * - Criada em 20/11/2013
 * 
 * @author Niury Martins Pereira
 */

include_once('class.ValidaCad.php');

class ValidaCadDoacao extends ValidaCad {

	public $cddoacao;
	public $cddoador;
	public $dtcontato;
	public $vldoacao;
	public $dtrec;
	public $cancelado;
	public $obsdoacao;
	public $nmresp;

	public function __construct($post, $con) {

		$this->con = $con;

		array_walk($post, 'array_insert_db');
		extract($post);

		$this->cddoacao = (int)$cddoacao;
		$this->cddoador = (int)$cddoador;
		$this->dtcontato = $dtcontato;
		$this->vldoacao = number_insert_mysql($vldoacao);
		$this->dtrec = $dtrec;
		$this->cancelado = $cancelado;
		$this->obsdoacao = $obsdoacao;
		$this->obsrecdoacao = $obsrecdoacao;
		$this->nmresp = trim($nmresp);

		if ($this->nmresp and !$this->cddoador) {
			$nmresp = $this->nmresp;
			$nmresp = split(' -- ', $nmresp);
			$this->cddoador = $nmresp[1];
			$this->nmresp = $nmresp[0];
		}

	}

	public function Valida() {
		//cddoador
		if (!$this->cddoador) {
			$this->setMsg('Favor informar o doador');
		}
		else {
			//verifica se o doador informado existe
			$sql = "select cddoador
					from tb_doador
					where cddoador = '$this->cddoador'";
			$sql = mysql_query($sql, $this->con);
			if (!mysql_num_rows($sql)) {
				$this->setMsg('O doador informado não é válido');
			}
		}

		//vldoacao
		if ($this->vldoacao == '') {
			//$this->setMsg('Favor informar o valor da doação');
			$this->vldoacao = '0.00';
		}

		//dtcontato
		if ($this->dtcontato and strlen($this->dtcontato) == 10) {
			$this->dtcontato = inverte_formato_data($this->dtcontato);

			if (!valida_data($this->dtcontato)) {
				$this->setMsg('A data de contato não é válida');
			}
			else {
				$this->dtcontato = "'" . $this->dtcontato . "'";
			}
		}
		else {
			$this->dtcontato = "null";
		}

		//dtresc
		if (!$this->dtrec) {
			$this->setMsg('Favor informar a data do recebimento');
		}
		else if (!valida_data(inverte_formato_data($this->dtrec))) {
			$this->setMsg('A data de recebimento não é válida');
		}
		else {
			$this->dtrec = inverte_formato_data($this->dtrec);
			$auxdtrec = true;
		}

		//valida se a doação está duplicada
		if ($auxdtrec and $this->vldoacao) {
			$sql = "select count(cddoacao) as tot
					from tb_doacao
					where cddoador = '$this->cddoador' and
						  vldoacao = '$this->vldoacao' and
						  dtrec = '$this->dtrec' and
						  cddoacao <> '$this->cddoacao' and
						  cancelado = 'N' and
						  excluido = 'N'";
			$sql = mysql_query($sql, $this->con);
			$sql = mysql_fetch_assoc($sql);

			if ($sql['tot']) {
				$this->setMsg('Já existe uma doação cadastrada para este doador contendo o mesmo valor e data de recebimento');
			}
		}

		//msg
		if ($this->getMsg()) {
			$this->resultado = false;
		}
		else {
			$this->resultado = true;
		}
	}
}