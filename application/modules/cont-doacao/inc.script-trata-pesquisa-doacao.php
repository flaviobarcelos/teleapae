<?php
//mostra_array($_POST);
if (isset($_POST['pesquisar'])) {

	$_SESSION['search']['lista-doacao'] = array();
	array_walk($_POST, 'array_insert_db');

	//cddoacao
	if ($_POST['cddoacao']) {
		$_SESSION['search']['lista-doacao']['cddoacao'] = trim($_POST['cddoacao']);
	}
	else {
		unset($_SESSION['search']['lista-doacao']['cddoacao']);
	}

	//doador
	if ($_POST['doador']) {
		$_SESSION['search']['lista-doacao']['doador'] = trim($_POST['doador']);
	}
	else {
		unset($_SESSION['search']['lista-doacao']['doador']);
	}

	//sgtpdoador
	if ($_POST['sgtpdoador']) {
		//echo 'Aqui';
		$_SESSION['search']['lista-doacao']['sgtpdoador'] = $_POST['sgtpdoador'];
	}
	else {
		unset($_SESSION['search']['lista-doacao']['sgtpdoador']);
	}

	//dtcontatoini
	if ($_POST['dtcontatoini']) {
		$_SESSION['search']['lista-doacao']['dtcontatoini'] = ($_POST['dtcontatoini']);
	}
	else {
		unset($_SESSION['search']['lista-doacao']['dtcontatoini']);
	}

	//dtcontatofim
	if ($_POST['dtcontatofim']) {
		$_SESSION['search']['lista-doacao']['dtcontatofim'] = ($_POST['dtcontatofim']);
	}
	else {
		unset($_SESSION['search']['lista-doacao']['dtcontatofim']);
	}

	//dtrecini
	if ($_POST['dtrecini']) {
		$_SESSION['search']['lista-doacao']['dtrecini'] = ($_POST['dtrecini']);
	}
	else {
		unset($_SESSION['search']['lista-doacao']['dtrecini']);
	}

	//dtrecfim
	if ($_POST['dtrecfim']) {
		$_SESSION['search']['lista-doacao']['dtrecfim'] = ($_POST['dtrecfim']);
	}
	else {
		unset($_SESSION['search']['lista-doacao']['dtrecfim']);
	}

	//operador
	if ($_POST['cdusuario']) {
		$_SESSION['search']['lista-doacao']['cdusuario'] = (int)$_POST['cdusuario'];
	}
	else {
		unset($_SESSION['search']['lista-doacao']['cdusuario']);
	}

	//status
	if ($_POST['cancelado']) {
		$_SESSION['search']['lista-doacao']['cancelado'] = $_POST['cancelado'];
	}
	else {
		unset($_SESSION['search']['lista-doacao']['cancelado']);
	}
}
else if (isset($_POST['limpar'])) {
	$_SESSION['search']['lista-doacao'] = array();
}

$where = '';
$link = '';
//monta o sql e link de pesquisa
//cddoacao
if ($_SESSION['search']['lista-doacao']['cddoacao']) {
	$cddoacao = $_SESSION['search']['lista-doacao']['cddoacao'];
	$where .= " and tb_doacao.cddoacao like '%$cddoacao%' ";
	$link .= '&amp;cddoacao=' . $cddoacao;
}

//doador
if ($_SESSION['search']['lista-doacao']['doador']) {
	$termo = $_SESSION['search']['lista-doacao']['doador'];
	$where .= " and (tb_doacao.nmfantasia like '%$termo%' or
				     tb_doacao.nmresponsavel like '%$termo%') ";
	$link .= '&amp;termo=' . urlencode($termo);
}

//sgtpdoador
if ($_SESSION['search']['lista-doacao']['sgtpdoador']) {
	$sgtpdoador = $_SESSION['search']['lista-doacao']['sgtpdoador'];
	$where .= " and tb_doacao.sgtpdoador = '$sgtpdoador' ";
	$link .= '&amp;sgtpdoador=' . $sgtpdoador;
}

//dtcontatoini && dtcontatofim
$dtcontatoini = $_SESSION['search']['lista-doacao']['dtcontatoini'];
$dtcontatofim = $_SESSION['search']['lista-doacao']['dtcontatofim'];

if ($dtcontatoini and $dtcontatofim) {
	$dtcontatoini = inverte_formato_data($dtcontatoini);
	$dtcontatofim = inverte_formato_data($dtcontatofim);
	$where .= " and tb_doacao.dtcontato between '$dtcontatoini' and '$dtcontatofim' ";
	$link .= "&amp;dtcontatoini=$dtcontatoini&amp;dtcontatofim=$dtcontatofim";
}
else if ($dtcontatoini) {
	$dtcontatoini = inverte_formato_data($dtcontatoini);
	$where .= " and tb_doacao.dtcontato = '$dtcontatoini' ";
	$link .= "&amp;dtcontatoini=$dtcontatoini";
}

//dtrecini && dtrecfim
$dtrecini = $_SESSION['search']['lista-doacao']['dtrecini'];
$dtrecfim = $_SESSION['search']['lista-doacao']['dtrecfim'];

if ($dtrecini and $dtrecfim) {
	$link .= "&amp;dtrecini=$dtrecini&amp;dtrecfim=$dtrecfim";
	$dtrecini = inverte_formato_data($dtrecini);
	$dtrecfim = inverte_formato_data($dtrecfim);
	$where .= " and tb_doacao.dtrec between '$dtrecini' and '$dtrecfim' ";

}
else if ($dtrecini) {
	$link .= "&amp;dtrecini=$dtrecini";
	$dtrecini = inverte_formato_data($dtrecini);
	$where .= " and tb_doacao.dtrec = '$dtrecini' ";
}

//cdusuario
if ($_SESSION['search']['lista-doacao']['cdusuario']) {
	$cdusuario = $_SESSION['search']['lista-doacao']['cdusuario'];
	$where .= " and tb_doacao.cdusuario = '$cdusuario' ";
	$link .= "&amp;cdusuario=$cdusuario";
}

//status
if ($_SESSION['search']['lista-doacao']['cancelado']) {
	$cancelado = $_SESSION['search']['lista-doacao']['cancelado'];
	$where .= " and tb_doacao.cancelado = '$cancelado' ";
	$link .= "&amp;cancelado=$cancelado";
}

//echo $where;

unset($cancelado, $cdusuario, $dtrecini, $dtrecfim, $dtcontatofim, $dtcontatoini,
$termo, $cddoacao);

//mostra_array($_SESSION['search']);