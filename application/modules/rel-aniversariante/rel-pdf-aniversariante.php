<?php
/**
 * Relatório de aniversariantes por periodo
 */

//validação de dados
extract($_POST);
if (!$dtini or !$dtfim) {
	require(INCLUDES . '/inc.header.php');
	echo msg('alert', 'Favor preencher corretamente as datas');
	require(INCLUDES . '/inc.footer.php');
	echo '<p style="text-align:center;"><a href="' , montalink('form-rel-aniversariante') , '">Voltar</a></p>';
	exit();
}
else {
	$dtini = inverte_formato_data($dtini);
	$dtfim = inverte_formato_data($dtfim);

	if ($dtini > $dtfim) {
		require(INCLUDES . '/inc.header.php');
		echo msg('alert', 'A data inicial não pode ser maior que a data final');
		require(INCLUDES . '/inc.footer.php');
		echo '<p style="text-align:center;"><a href="' , montalink('form-rel-aniversariante') , '">Voltar</a></p>';
		exit();
	}
	else {
		//echo $dtini.'<br />';
		$dtini = substr($dtini, 5, 10);
		$dtfim = substr($dtfim, 5, 10);
		//die($dtini);
	}
}

$where = '';

if ($ativo) {
	$where = " and tb_doador.ativo = '$ativo' ";
}

$sql = "select *
		from tb_doador
		where date_format(dtniverresp, '%m-%d') between '$dtini' and '$dtfim' $where
		order by dtniverresp desc, nmresponsavel";
$sql = mysql_query($sql, $con);
if (!mysql_num_rows($sql)) {
	require(INCLUDES . '/inc.header.php');
	echo msg('alert', 'Nenhum aniversariante encontrado');
	require(INCLUDES . '/inc.footer.php');
	echo '<p style="text-align:center;"><a href="' , montalink('form-rel-aniversariante') , '">Voltar</a></p>';
	exit();
}

include_once(CLASSES . '/pdf/pdf.php');
$pdf = new PDF('L','cm','A4');
$pdf->AliasNbPages('{total}');
$pdf->AddPage();
$pdf->SetMargins(0.5, 0.5, 0.5, 0.5);
$pdf->SetTitle('RELAÇÃO DE DOADORES POR STATUS');
$pdf->Image(IMG . '/logo_relatorio.jpg','1.5','0.75', 4);
$pdf->SetY(1);
$pdf->SetFont('arial','B', 11);
$pdf->SetTextColor(00, 00, 00);
$pdf->Cell(7,1.8, '', 0, 0,'C');
$pdf->Cell(21.5,0.6, NM_SISTEMA, 1, 1,'C');
$pdf->Cell(7,1.8,'',0,'C');
$pdf->SetFont('arial','', 8);
$pdf->Cell(21.5, 0.6, DS_SISTEMA, 1, 1, 'C');
$pdf->Cell(7, 1.8, '', 0, 'C');
$pdf->Cell(19, 0.6, 'IMPRESSO POR: ' . $_SESSION['logado']['usuario']['nmusuario'] . ' - ' . date('d/m/Y').' '.date('H:i:s'), 0, 1, 'R');
$pdf->Ln(1);

$pdf->SetFont('arial','B', 14);
$pdf->Cell(28.5, 1, 'RELAÇÃO DE ANIVERSARIANTES', 0, 1, 'C');

$pdf->SetFont('arial','B', 8);
$pdf->SetFillColor(199, 199, 199);
$pdf->Cell(1, 0.5, 'COD.', 1, 0, 'C', true);
$pdf->Cell(2, 0.5, 'DT. NIVER.', 1, 0, 'C', true);
$pdf->Cell(6.5, 0.5, 'RESPONSÁVEL', 1, 0, 'C', true);
$pdf->Cell(6, 0.5, 'NOME FANTASIA', 1, 0, 'C', true);
$pdf->Cell(6.5, 0.5, 'TELEFONE(S)', 1, 0, 'C', true);
$pdf->Cell(6.5, 0.5, 'E-MAIL', 1, 1, 'C', true);


$pdf->SetFont('arial','', 8);
while ($res = mysql_fetch_assoc($sql)) {
	$pdf->SetFillColor(199, 199, 199);
	$pdf->Cell(1, 0.5, $res['cddoador'], 1, 0, 'C');
	$pdf->Cell(2, 0.5, inverte_formato_data($res['dtniverresp'], '/'), 1, 0, 'C');
	
	$pdf->Cell(6.5, 0.5, $res['nmresponsavel'], 1, 0, 'L');
	$pdf->Cell(6, 0.5, $res['nmfantasia'], 1, 0, 'L');
	
	$telefone2 = $res['telefone2'] != '' ? ' / ' . $res['telefone2'] : '' ;
	$telefone3 = $res['telefone3'] != '' ? ' / ' . $res['telefone3'] : '' ;
	
	$pdf->Cell(6.5, 0.5, $res['telefone1'] . $telefone2 . $telefone3, 1, 0, 'C');
	$pdf->Cell(6.5, 0.5, $res['email'], 1, 1, 'L');
}

$pdf->SetFont('arial','B', 8);
$pdf->SetFillColor(199, 199, 199);
$pdf->Cell(1, 0.5, 'COD.', 1, 0, 'C', true);
$pdf->Cell(2, 0.5, 'DT. NIVER', 1, 0, 'C', true);
$pdf->Cell(6.5, 0.5, 'RESPONSÁVEL', 1, 0, 'C', true);
$pdf->Cell(6, 0.5, 'NOME FANTASIA', 1, 0, 'C', true);
$pdf->Cell(6.5, 0.5, 'TELEFONE(S)', 1, 0, 'C', true);
$pdf->Cell(6.5, 0.5, 'E-MAIL', 1, 1, 'C', true);

$pdf->Output();