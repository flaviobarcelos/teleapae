<?php
/**
 * Arquivo respons�vel por realizar a conex�o com o banco de dados
 * - Criado em 13/01/2010
 * 
 * @author Niury Martins - http://niurymartins.com.br
 */

if (HOMOLOGACAO) {
	$server = '127.0.0.1';
	$user = 'root';
	$pass = '';
	$dbname = 'db_teleapae';
}
else {
	$server = '127.0.0.1';
	$user = 'root';
	$pass = '';
	$dbname = 'db_teleapae';
}

$con = mysql_connect($server, $user, $pass) or die('Desculpe, o servi�o est� temporarimente indispon�vel');
if ($con) {
	$con_sma = $con;
	$db = mysql_select_db($dbname, $con) or die(mysql_error());
	//mysql_set_charset('utf8', $con);
}

unset($server, $user, $pass, $dbname);