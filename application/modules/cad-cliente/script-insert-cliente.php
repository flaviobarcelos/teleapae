<?php
/**
 * Script de inserção de clientes
 * - Criado em 18/01/2010
 * 
 * @author Niury Martins Pereira
 */

if (isset($_POST['salvar'])) {

	include_once(CLASSES . '/class.ValidaCadCliente.php');
	$_SESSION['postcadcliente'] = $_POST;
	extract($_POST);

	$validacao = new ValidaCadCliente($_POST);
	$validacao->executaValidacao();
	if (!$validacao->obtemValidacao()) {
		set_session_msg('cadcliente', 'alert', $validacao->obtemErros());
	}
	else {
		
		if (!((int)$validacao->cdestadocivil)) {
			$validacao->cdestadocivil = 'null';
		}
		else {
			$validacao->cdestadocivil = "'{$validacao->cdestadocivil}'";
		}
		
		$sql = "insert into tb_cliente
				(nmcliente, email, recebernews, receber_sms,
				 senha, cdcidade, endereco,
				 nmbairro, cep, complemento, 
				 sexo, dtnascimento, cdestadocivil,
				 telcel, telresid, num, ativo, 
				 observacao)
				 values
				('{$validacao->nmcliente}', '{$validacao->email}', '{$validacao->recebernews}', '{$validacao->receber_sms}',
				 '{$validacao->senha}', '{$validacao->cdcidade}', '{$validacao->endereco}',
				 '{$validacao->nmbairro}', '{$validacao->cep}', '{$validacao->complemento}',
				 '{$validacao->sexo}', '{$validacao->dtnascimento}', {$validacao->cdestadocivil},
				 '{$validacao->telcel}', '{$validacao->telresid}', '{$validacao->num}', 
				 '{$validacao->ativo}', '{$validacao->observacao}')";
		if (mysql_query($sql, $con)) {
			$cdcliente = mysql_insert_id($con);
			logCliente($cdcliente, $_SESSION['logado']['usuario']['cdusuario'], 'I');
			set_session_msg('cadcliente', 'ok', MSG_OK);
		}
		else {
			set_session_msg('cadcliente', 'error', mysql_error($con));
		}
	}
}

$aux = $cdcliente ? '&cdcliente=' . $cdcliente : '' ;
header('location: ' . montalink('form-cad-cliente', '&') . $aux);