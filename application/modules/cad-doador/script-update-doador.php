<?php
/**
 * Script de inserção de empresa
 * - Criado em 05/12/2013
 * 
 * @author Niury Martins - http://niurymartins.com.br
 */

include_once(CLASSES . '/class.ValidaCadDoador.php');

if (isset($_POST['salvar'])) {

	$_SESSION['postcaddoador'] = $_POST;
	$v = new ValidaCadDoador($_POST, $con);
	//die(mostra_array($v));
	$v->Valida();
	
	$cddoador = $v->cddoador;

	if (!$v->getResultado()) {
		set_session_msg('caddoador', 'alert', $v->getMsg());
	}
	//realiza a inserção
	else {
		$sql = "update tb_doador set
					cdtppessoa = '$v->cdtppessoa', 
					cpf = '$v->cpf', 
					nmresponsavel = '$v->nmresponsavel', 
					dtniverresp = $v->dtniverresp,
					dtniverconjuge = $v->dtniverconjuge, 
					nmconjuge = '$v->nmconjuge', 
					cnpj = '$v->cnpj', 
					razaosocial = '$v->razaosocial',
					nmfantasia = '$v->nmfantasia', 
					cep = '$v->cep', 
					cdcidade = '$v->cdcidade', 
					nmbairro = '$v->nmbairro', 
					endereco = '$v->endereco',
					num = NULL, 
					email = '$v->email', 
					telefone1 = '$v->telefone1', 
					telefone2 = '$v->telefone2', 
					telefone3 = '$v->telefone3',
					cdtpdoador = '$v->cdtpdoador', 
					diarec = '$v->diarec', 
					vldoacao = '$v->vldoacao', 
					cdusuario = $v->cdusuario,
					obsdoador = '$v->obsdoador', 
					obsrecibo = '$v->obsrecibo', 
					ativo = '$v->ativo'
				where
					cddoador = '$v->cddoador'";
		//die($sql);
		
		if (mysql_query($sql, $con)) {
			set_session_msg('caddoador', 'ok', MSG_ALTERACAO);
			unset($_SESSION['postcaddoador']);
			ReplicaDadosDoador($v->cddoador);
		}
		else {
			set_session_msg('caddoador', 'error', MSG_ERROR . '<br />' . mysql_error($con));
		}
	}
}

$aux = $cddoador ? '&cddoador=' . $cddoador : '' ;
header('location: ' . montalink('form-cad-doador', '&') . $aux);