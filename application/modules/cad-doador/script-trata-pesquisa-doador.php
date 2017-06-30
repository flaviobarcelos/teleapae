<?php
if (!isset($_SESSION['search']['lista-doador'])) {
	$_SESSION['search']['lista-doador'] = array();
}

//trata os posts
if (isset($_POST['pesquisar'])) {
	if ($_POST['cdtpdoador']) {
		$_SESSION['search']['lista-doador']['cdtpdoador'] = (int)$_POST['cdtpdoador'];
	}
	else {
		unset($_SESSION['search']['lista-doador']['cdtpdoador']);
	}

	if ($_POST['cdusuario']) {
		$_SESSION['search']['lista-doador']['cdusuario'] = (int)$_POST['cdusuario'];
	}
	else {
		unset($_SESSION['search']['lista-doador']['cdusuario']);
	}

	if ($_POST['termo']) {
		$_SESSION['search']['lista-doador']['termo'] = trim($_POST['termo']);
	}
	else {
		unset($_SESSION['search']['lista-doador']['termo']);
	}
	
	//dtcontatoini
	if ($_POST['dtcontatoini']) {
		$_SESSION['search']['lista-doador']['dtcontatoini'] = ($_POST['dtcontatoini']);
	}
	else {
		unset($_SESSION['search']['lista-doador']['dtcontatoini']);
	}

	//dtcontatofim
	if ($_POST['dtcontatofim']) {
		$_SESSION['search']['lista-doador']['dtcontatofim'] = ($_POST['dtcontatofim']);
	}
	else {
		unset($_SESSION['search']['lista-doador']['dtcontatofim']);
	}
	
	//ativo
	if ($_POST['ativo']) {
		$_SESSION['search']['lista-doador']['ativo'] = $_POST['ativo'];
	}
	else {
		unset($_SESSION['search']['lista-doador']['ativo']);
	}
	
	//volta para pсgina 1
	//$_SEESION['pag-lista-doador'] = 1;
}
else if ($_POST['limpar']) {
	$_SESSION['search']['lista-doador'] = array();
}

$where = '';
$link = '';
//cria clausula where e link paginaчуo
//cdtpdoador
if ($_SESSION['search']['lista-doador']['cdtpdoador']) {
	$where .= " and tb_doador.cdtpdoador = '" . $_SESSION['search']['lista-doador']['cdtpdoador'] . "' ";
	$link .= '&amp;cdtpdoador=' . $_SESSION['search']['lista-doador']['cdtpdoador'];
}

//cdusuario
if ($_SESSION['search']['lista-doador']['cdusuario']) {
	$where .= " and tb_doador.cdusuario = '" . $_SESSION['search']['lista-doador']['cdusuario'] . "' ";
	$link .= '&amp;cdusuario=' . $_SESSION['search']['lista-doador']['cdusuario'];
}

//termo
if ($_SESSION['search']['lista-doador']['termo']) {
	$termo = $_SESSION['search']['lista-doador']['termo'];
	$where .= " and (tb_doador.cddoador = '$termo' or
				     tb_doador.cnpj = '$termo' or
				     tb_doador.cpf = '$termo' or
				     tb_doador.nmfantasia like '%$termo%' or
				     tb_doador.razaosocial like '%$termo%' or
				     tb_doador.nmresponsavel like '%$termo%' or
				     tb_doador.telefone1 like '%$termo%' or
				     tb_doador.telefone2 like '%$termo%' or
				     tb_doador.telefone3 like '%$termo%' or
				     tb_doador.endereco like '%$termo%' or
				     tb_doador.email like '%$termo%' or
				     tb_cidade.nmcidade like '%$termo%' or
				     tb_doador.nmbairro like '%$termo%') ";
	$link .= '&amp;termo=' . urlencode($_SESSION['search']['lista-doador']['termo']);
}

//dtcontatoini && dtcontatofim
$dtcontatoini = $_SESSION['search']['lista-doador']['dtcontatoini'];
$dtcontatofim = $_SESSION['search']['lista-doador']['dtcontatofim'];

if ($dtcontatoini and $dtcontatofim) {
	$dtcontatoini = inverte_formato_data($dtcontatoini);
	$dtcontatofim = inverte_formato_data($dtcontatofim);
	$where .= " and tb_doador.ultdtcontato between '$dtcontatoini' and '$dtcontatofim' ";
	$link .= "&amp;dtcontatoini=$dtcontatoini&amp;dtcontatofim=$dtcontatofim";
}
else if ($dtcontatoini) {
	$dtcontatoini = inverte_formato_data($dtcontatoini);
	$where .= " and tb_doador.ultdtcontato = '$dtcontatoini' ";
	$link .= "&amp;dtcontatoini=$dtcontatoini";
}

//ativo
if ($_SESSION['search']['lista-doador']['ativo']) {
	$where .= " and tb_doador.ativo = '" . $_SESSION['search']['lista-doador']['ativo'] . "' ";
	$link .= "&amp;ativo=" . $_SESSION['search']['lista-doador']['ativo'];
}

unset($dtcontatoini, $dtcontatofim, $termo, $cdtpdoador, $cdusuario);

if ($where) {
	$where = ' where 1=1 ' . $where;
}