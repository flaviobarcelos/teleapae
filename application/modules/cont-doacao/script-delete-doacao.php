<?php
/**
 * Script de exclusao de doacao
 * - Criado em 09/12/2013
 */

if ($_GET['cddoacao']) {
	
	$cddoacao = (int)$_GET['cddoacao'];
	
	$sql = "update tb_doacao set
			excluido = 'S',
			dtexclusao = '" . date('Y-m-d H:m:s') . "',
			nmusuarioexclusao = '{$_SESSION['logado']['usuario']['nmusuario']}'
			where cddoacao = '$cddoacao'";
	if (mysql_query($sql, $con)) {
		set_session_msg('caddoacao', 'ok', MSG_DELETE);
		ReplicaDadosDoacoes(0, $cddoacao);
	}
	else {
		set_session_msg('caddoacao', 'error', MSG_ERROR . '<br />' . mysql_error($con));
	}
	
	$pag = isset($_GET['pag']) ? $_GET['pag'] : 'lista-doacao' ;
	$aux = $_GET['cddoador'] ? '&cddoador=' . $_GET['cddoador'] : '' ;
	header('location: ' . montalink($pag, '&') . $aux);
}