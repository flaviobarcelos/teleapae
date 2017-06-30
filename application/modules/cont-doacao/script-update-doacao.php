<?php
/**
 * Script de insercao de doacao
 * - Criado em 09/12/2013
 * 
 * @author Niury Martins - http://niurymartins.com.br
 */

include_once(CLASSES . '/class.ValidaCadDoacao.php');

if (isset($_POST['salvar'])) {

	$_SESSION['postcaddoacao'] = $_POST;
	$v = new ValidaCadDoacao($_POST, $con);
	$v->Valida();
	
	$cddoacao = $v->cddoacao;

	if (!$v->getResultado()) {
		set_session_msg('caddoacao', 'alert', $v->getMsg());
	}
	//realiza a inserção
	else {
		$sql = "update tb_doacao set
				cddoador = '$v->cddoador',
				dtcontato = $v->dtcontato,
				dtrec = '$v->dtrec',
				obsdoacao = '$v->obsdoacao',
				vldoacao = '$v->vldoacao',
				cancelado = '$v->cancelado',
				dtupdate = now()
				where cddoacao = '$v->cddoacao'";
		//die($sql);
		
		if (mysql_query($sql, $con)) {
			set_session_msg('caddoacao', 'ok', MSG_ALTERACAO);
			ReplicaDadosDoacoes($v->cddoador);
			ReplicaDadosDoador($v->cddoador);
		}
		else {
			set_session_msg('caddoacao', 'error', MSG_ERROR . '<br />' . mysql_error($con));
		}
	}
}

$aux = $cddoacao ? '&cddoacao=' . $cddoacao : '' ;
header('location: ' . montalink('form-cad-doacao', '&') . $aux);