<?php
/**
 * Classe de validação de cadastro de cliente
 * - Criada em 18/01/2010
 * 
 * @author Niury Martins Pereira
 */
class ValidaCadCliente {

	public $cdcliente;
	public $nmcliente;
	public $sexo;
	public $cdestadocivil;
	public $dtnascimento;
	public $cdcidade;
	public $nmbairro;
	public $endereco;
	public $num;
	public $cep;
	public $complemento;
	public $email;
	public $telcel;
	public $telresid;
	public $recebernews;
	public $receber_sms;
	public $ativo;
	public $senha;
	public $senhamd5;
	public $observacao;

	public function __construct($post) {
		array_walk($post, 'array_insert_db');
		extract($post);

		$this->cdcliente = $cdcliente;
		$this->nmcliente = $nmcliente;
		$this->sexo = $sexo;
		$this->cdestadocivil = $cdestadocivil;
		$this->dtnascimento = $dtnascimento;
		$this->cdcidade = $cdcidade;
		$this->nmbairro = $nmbairro;
		$this->endereco = $endereco;
		$this->num = $num;
		$this->cep = $cep;
		$this->complemento = $complemento;
		$this->email = $email;
		$this->telcel = $telcel;
		$this->telresid = $telresid;
		$this->recebernews = $recebernews;
		$this->receber_sms = $receber_sms;
		$this->ativo = $ativo;
		$this->senha = $senha;
		$this->senhamd5 = $senhamd5;
		$this->observacao = $observacao;
	}

	/**
	 * Retorna a validação realizada
	 *
	 * @return boolean
	 */
	public function obtemValidacao() {
		return $this->valid;
	}

	/**
	 * Retorna os erros encontrados durante a validação
	 *
	 * @return string
	 */
	public function obtemErros() {
		return $this->msg;
	}

	/**
	 * Valida se os dados estão preenchidos corretamente
	 *
	 * @return boolean
	 */
	public function executaValidacao() {

		//validação de campos obrigatórios
		if (!$this->nmcliente) {
			set_msg($this->msg, 'Favor informar o nome');
		}

		if (!$this->sexo) {
			set_msg($this->msg, 'Favor informar o sexo');
		}

		if (!$this->cdcidade) {
			set_msg($this->msg, 'Favor selecionar a cidade');
		}

		if (!$this->email) {
			set_msg($this->msg, 'Favor informar o e-email');
		}
		else if (!valida_email($this->email)) {
			set_msg($this->msg, 'O e-mail informado não é válido');
		}

		if (!$this->senha and !$this->senhamd5) {
			set_msg($this->msg, 'Favor informar a senha');
		}
		else if ((strlen($this->senha) < 6 or strlen($this->senha) > 16) and !$this->senhamd5) {
			set_msg($this->msg, 'A senha deve conter no mínimo 6 e no máximo 16 caracteres');
		}
		else if ($this->senha) {
			$this->senha = char_senha($this->senha);
		}

		//tratamento de outros campos
		//data de nascimento
		if ($this->dtnascimento != '' and !valida_data(inverte_formato_data($this->dtnascimento))) {
			set_msg($this->msg, 'A data de nascimento informada não é válida');
		}
		else if ($this->dtnascimento != '') {
			$this->dtnascimento = inverte_formato_data($this->dtnascimento);
		}

		//receber news
		if (!$this->recebernews) {
			$this->recebernews = 'N';
		}
		
		//receber sms
		if (!$this->receber_sms) {
			$this->receber_sms = 'N';
		}

		if ($this->msg != '') {
			$this->valid = false;
		}
		else {
			$this->valid = true;
		}
	}
}