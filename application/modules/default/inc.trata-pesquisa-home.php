<?php
/**
 * Script para tratar o filtro de pesquisa na home
 * - 07/06/2011
 * 
 * @author Niury Martins - http://niurymartins.com.br
 */
if (isset($_POST['pesquisar'])) {
	//verifica se existe a sessão de pesquisa para esta página
	if (!is_array($_SESSION['search']['default-home'])) {
		$_SESSION['search']['default-home'] = array();
	}
		
	//nmoferta
	if (trim($_POST['nmoferta'])) {
		$_SESSION['search']['default-home']['nmoferta'] = addslashes(trim($_POST['nmoferta']));
	}
	else {
		unset($_SESSION['search']['default-home']['nmoferta']);
	}

	//cdcidade
	if ((int)$_POST['cdcidade']) {
		$_SESSION['search']['default-home']['cdcidade'] = (int)$_POST['cdcidade'];
	}
	else {
		unset($_SESSION['search']['default-home']['cdcidade']);
	}
	
	//período - dtinicio
	if ($_POST['dtinicio']) {
		$_SESSION['search']['default-home']['dtinicio'] = addslashes(trim($_POST['dtinicio']));
	}
	else {
		unset($_SESSION['search']['default-home']['dtinicio']);
	}
	
	//período - dttermino
	if ($_POST['dttermino']) {
		$_SESSION['search']['default-home']['dttermino'] = addslashes(trim($_POST['dttermino']));
	}
	else {
		unset($_SESSION['search']['default-home']['dttermino']);
	}
}
else if (isset($_POST['limpar'])) {
	unset($_SESSION['search']['default-home']);
}

//caso tenha dados para pesquisa na sessão de pesquisa desta página
$where_pesquisa = '';
if ($_SESSION['search']['default-home']['cdcidade']) {
	$cdcidade = $_SESSION['search']['default-home']['cdcidade'];
	if ($cdcidade) {
		$where_pesquisa = " and tb_cidade.cdcidade = '$cdcidade' ";
	}
}

if ($_SESSION['search']['default-home']['nmoferta']) {
	$nmoferta = $_SESSION['search']['default-home']['nmoferta'];
	if ($nmoferta) {
		$where_pesquisa .= " and tb_oferta.nmoferta like '%$nmoferta%' ";
	}
}

//dtinicio
if ($_SESSION['search']['default-home']['dtinicio']) {
	$dtinicio = inverte_formato_data($_SESSION['search']['default-home']['dtinicio']);
	if (valida_data($dtinicio)) {
		$where_pesquisa .= " and tb_oferta.dtinicio <= '$dtinicio' ";
	}
}

//dttermino
if ($_SESSION['search']['default-home']['dttermino']) {
	$dttermino = inverte_formato_data($_SESSION['search']['default-home']['dttermino']);
	if (valida_data($dttermino)) {
		$where_pesquisa .= " and tb_oferta.dttermino >= '$dttermino' ";
	}
}