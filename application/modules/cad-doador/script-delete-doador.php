<?php

if ($_GET['cddoador']) {

	$cddoador = (int)$_GET['cddoador'];

	$sql = "delete from tb_doacao where cddoador = '$cddoador'";
	mysql_query($sql, $con);
	
	$sql = "delete from tb_doador where cddoador = '$cddoador'";
	mysql_query($sql, $con);

	set_session_msg('caddoador', 'ok', MSG_DELETE);
	
	$pag = isset($_GET['pag']) ? $_GET['pag'] : 'lista-doador' ;
	$aux = $_GET['cddoador'] ? '&cddoador=' . $_GET['cddoador'] : '' ;
	header('location: ' . montalink($pag, '&') . $aux);
}