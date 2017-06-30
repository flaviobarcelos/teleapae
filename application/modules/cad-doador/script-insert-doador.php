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
	$v->Valida();

	if (!$v->getResultado()) {
		set_session_msg('caddoador', 'alert', $v->getMsg());
	}
	//realiza a inserção
	else {
		$sql = "insert into tb_doador
				(cdtppessoa, cpf, nmresponsavel, dtniverresp,
				 dtniverconjuge, nmconjuge, cnpj, razaosocial,
				 nmfantasia, cep, cdcidade, nmbairro, endereco,
				 num, email, telefone1, telefone2, telefone3,
				 cdtpdoador, diarec, vldoacao, cdusuario,
				 obsdoador, obsrecibo, ativo)
				values
				('$v->cdtppessoa', '$v->cpf', '$v->nmresponsavel', $v->dtniverresp,
				 $v->dtniverconjuge, '$v->nmconjuge', '$v->cnpj', '$v->razaosocial',
				 '$v->nmfantasia', '$v->cep', '$v->cdcidade', '$v->nmbairro', '$v->endereco',
				 NULL, '$v->email', '$v->telefone1', '$v->telefone2', '$v->telefone3',
				 '$v->cdtpdoador', '$v->diarec', '$v->vldoacao', $v->cdusuario,
				 '$v->obsdoador', '$v->obsrecibo', '$v->ativo')";
		//die($sql);
		
		if (mysql_query($sql, $con)) {
			set_session_msg('caddoador', 'ok', MSG_OK);
			$cddoador = mysql_insert_id($con);
		}
		else {
			set_session_msg('caddoador', 'error', MSG_ERROR . '<br />' . mysql_error($con));
		}
	}
}

$aux = $cddoador ? '&cddoador=' . $cddoador : '' ;
header('location: ' . montalink('form-cad-doador', '&') . $aux);