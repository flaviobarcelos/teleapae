<?php

/**
 * Arquivo responsсvel por realizar as principais configuraчѕes do sistema.
 * Toda requisiчуo passa por este arquivo.
 * - Criado em 06/09/2010
 * 
 * @author Niury Martins Pereiraassasas
 */

session_start();
setlocale(LC_ALL, 'pt_BR');
header("Content-Type: text/html; charset=ISO-8859-1", true);

//prepara a sessao de pesquisa
if (!isset($_SESSION['pesquisa'])) {
	$_SESSION['pesquisa'] = array();
}

require('./application/conf/conf.directories.php');
require(CONF . '/conf.constants.php');
require('./application/conf/conf.connection.php');
require(INCLUDES . '/inc.functions.php');
require(INCLUDES . '/inc.functions_log.php');

$pagina = getpage();

//verifica se o usuсrio nуo estс logado
if (!isauth()) {

	//verifica se a requisiчуo щ do mѓdulo de login
	if (strpos($pagina, 'auth')) {
		require($pagina);
	}
	else {
		require(MODULES . '/auth/form-login.php');
	}
}
else {
	if (!is_file($pagina)) {
		require(MODULES . '/error/404.php');
	}
	else if (!checkpermission($pagina)) {
		require(MODULES . '/error/403.php');
	}
	else {
		require($pagina);
	}
}