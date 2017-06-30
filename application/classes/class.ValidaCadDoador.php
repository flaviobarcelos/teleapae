<?php
/**
 * Classe de validação de cadastro de doador
 * - Criada em 20/11/2013
 * 
 * @author Niury Martins Pereira
 */

include_once('class.ValidaCad.php');

class ValidaCadDoador extends ValidaCad {

	public $cddoador;
	public $cdtppessoa;
	public $cpf;
	public $nmresponsavel;
	public $dtniverresp;
	public $nmconjuge;
	public $dtniverconjuge;
	public $cnpj;
	public $razaosocial;
	public $nmfantasia;

	public $cep;
	public $cdcidade;
	public $nmbairro;
	public $endereco;
	public $num;
	public $complemento;

	public $email;
	public $telefone1;
	public $telefone2;
	public $telefone3;

	public $cdtpdoador;
	public $diarec;
	public $vldoacao;
	public $cdusuario;
	public $obsdoador;
	public $obsrecibo;

	public $ativo;

	public function __construct($post, $con) {

		array_walk($post, 'array_insert_db');
		//mostra_array($post);
		extract($post);
		
		$this->con = $con;

		$this->cddoador = (int)$cddoador;
		$this->cdtppessoa = $cdtppessoa;
		$this->cpf = $cpf;
		$this->nmresponsavel = $nmresponsavel;
		$this->dtniverresp = $dtniverresp;
		$this->nmconjuge = $nmconjuge;
		$this->dtniverconjuge = $dtniverconjuge;
		$this->cnpj = $cnpj;
		$this->razaosocial = $razaosocial;
		$this->nmfantasia = $nmfantasia;

		$this->cep = $cep;
		$this->cdcidade = $cdcidade;
		$this->nmbairro = $nmbairro;
		$this->endereco = $endereco;
		$this->num = $num;
		$this->complemento = $complemento;

		$this->email = $email;
		$this->telefone1 = $telefone1;
		$this->telefone2 = $telefone2;
		$this->telefone3 = $telefone3;

		$this->cdtpdoador = $cdtpdoador;
		$this->diarec = $diarec;
		$this->vldoacao = number_insert_mysql($vldoacao);
		$this->cdusuario = (int)$cdusuario ? (int)$cdusuario : 'null' ;
		$this->obsdoador = $obsdoador;
		$this->obsrecibo = $obsrecibo;

		$this->ativo = $ativo;
	}

	/**
	 * Valida se os dados estão preenchidos corretamente
	 *
	 * @return boolean
	 */
	public function Valida() {

		//tppessoa
		if ($this->tppessoa == 'F') {
			if ($this->cpf and !valida_cpf($this->cpf)) {
				$this->setMsg('CPF inválido');
			}
		}
		
		//nome do responsável
		if (!$this->nmresponsavel) {
			$this->setMsg('Favor informar o nome do responsável');	
		}
		else {
			$sql = "select count(cddoador) as tot
					from tb_doador
					where nmresponsavel like '{$this->nmresponsavel}' and
						  cddoador <> {$this->cddoador}";
			$sql = mysql_query($sql, $this->con);
			$sql = mysql_fetch_assoc($sql);
			if ($sql['tot']) {
				$this->setMsg('O responsável informado já encontra-se cadastrado');
			}
		}
		
		//dtniverresp
		if ($this->dtniverresp) {
			$this->dtniverresp = inverte_formato_data($this->dtniverresp);
			
			if (!valida_data($this->dtniverresp)) {
				$this->setMsg('Data de aniversário do responsável inválida');
			}
			else {
				$this->dtniverresp = "'" . $this->dtniverresp . "'";
			}
		}
		else {
			$this->dtniverresp = 'NULL';
		}
				
		//dtniverconjuge
		if ($this->dtniverconjuge) {
			$this->dtniverconjuge = inverte_formato_data($this->dtniverconjuge);
			
			if (!valida_data($this->dtniverconjuge)) {
				$this->setMsg('Data de aniversário do côjuge inválida');
			}
			else {
				$this->dtniverconjuge = "'" . $this->dtniverconjuge . "'";	
			}
		}
		else {
			$this->dtniverconjuge = 'NULL';
		}
		
		//cdcidade
		if (!$this->cdcidade) {
			$this->setMsg('Favor informar a cidade');
		}
		
		//nmbairro
		if (!$this->nmbairro) {
			$this->setMsg('Favor informar o nome do bairro');
		}
		
		//endereco
		if (!$this->endereco) {
			$this->setMsg('Favor informar o endereço');
		}
		
		//cdtpdoador
		if (!$this->cdtpdoador) {
			$this->setMsg('Favor informar o tipo de doador');
		}
		
		//diadoacao
		if ($this->diarec and ($this->diarec < 1 or $this->diarec > 31)) {
			$this->setMsg('Dia para recebimento inválido');
			$diarec = true;
		}
		else {
			$diarec = false;
			if ($this->diarec) {
				$this->diarec = (int)$this->diarec;
			}
		}
		
		//doacao
		if ($diarec and $this->vldoacao <= 0) {
			$this->setMsg('Valor para recebimento inválido');
		}
		
		if ($this->msg != '') {
			$this->resultado = false;
		}
		else {
			$this->resultado = true;
		}
	}
}