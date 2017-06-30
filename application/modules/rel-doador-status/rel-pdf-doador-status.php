<?php
/**
 * Relatório de doadores por status
 */

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
$pdf->Cell(28.5, 1, 'RELAÇÃO DE DOADORES', 0, 1, 'C');

//totalização de doadores
$sql = "select
		      count(tb_doador.cddoador) as tot
		from 
		     tb_doador
		     inner join tb_tpdoador on tb_doador.cdtpdoador = tb_tpdoador.cdtpdoador
		where 
			tb_doador.ativo = 'S'";
$sql = mysql_query($sql, $con);
$sql = mysql_fetch_assoc($sql);
$totativos = $sql['tot'];

$sql = "select
		      tb_tpdoador.sgtpdoador,
		      count(tb_doador.cddoador) as tot
		from 
		     tb_doador
		     inner join tb_tpdoador on tb_doador.cdtpdoador = tb_tpdoador.cdtpdoador 
		where
		     tb_doador.ativo = 'S'
		group by 
		      tb_tpdoador.sgtpdoador
		order by
		      tb_tpdoador.sgtpdoador";
$sql = mysql_query($sql, $con);
$x = 0;

$ativos = '';
while ($res = mysql_fetch_assoc($sql)) {

	if ($x > 0) {
		$ativos .= ' | ';
	}

	$ativos .= $res['sgtpdoador'] . ' = ' . $res['tot'];
	$x++;
}

//inativos
$sql = "select
		      count(tb_doador.cddoador) as tot
		from 
		     tb_doador
		     inner join tb_tpdoador on tb_doador.cdtpdoador = tb_tpdoador.cdtpdoador
		where 
			tb_doador.ativo = 'N'";
$sql = mysql_query($sql, $con);
$sql = mysql_fetch_assoc($sql);
$totinativos = $sql['tot'];

$sql = "select
		      tb_tpdoador.sgtpdoador,
		      count(tb_doador.cddoador) as tot
		from 
		     tb_doador
		     inner join tb_tpdoador on tb_doador.cdtpdoador = tb_tpdoador.cdtpdoador 
		where
		     tb_doador.ativo = 'N'
		group by 
		      tb_tpdoador.sgtpdoador
		order by
		      tb_tpdoador.sgtpdoador";
$sql = mysql_query($sql, $con);
$x = 0;
$inativos = '';
while ($res = mysql_fetch_assoc($sql)) {

	if ($x > 0) {
		$inativos .= ' | ';
	}

	$inativos .= $res['sgtpdoador'] . ' = ' . $res['tot'];
	$x++;
}

$pdf->Ln(1);
$pdf->SetFont('arial','B', 8);
$pdf->Cell(20, 0.5, 'TOTALIZAÇÃO DE DOADORES', 0, 1);
$pdf->Cell(1.2, 0.5, 'ATIVO: ');
$pdf->Cell(10, 0.5, $totativos, 0);
$pdf->SetFont('arial','B', 8);
$pdf->Cell(1.5, 0.5, 'INATIVO: ', 0, 0);
$pdf->Cell(5, 0.5, $totinativos, 0, 1);
$pdf->SetFont('arial','', 8);
$pdf->Cell(11.2, 0.5, $ativos, 0);
$pdf->SetFont('arial','', 8);
$pdf->Cell(10, 0.5, $inativos, 0, 1);

$pdf->SetFont('arial','B', 8);
$pdf->SetFillColor(199, 199, 199);
$pdf->Cell(1, 0.5, 'COD.', 1, 0, 'C', true);
$pdf->Cell(7, 0.5, 'RESPONSÁVEL', 1, 0, 'C', true);
$pdf->Cell(7, 0.5, 'NOME FANTASIA', 1, 0, 'C', true);
$pdf->Cell(7, 0.5, 'TELEFONE(S)', 1, 0, 'C', true);
$pdf->Cell(1, 0.5, 'ATIVO', 1, 0, 'C', true);
$pdf->Cell(1, 0.5, 'TIPO', 1, 0, 'C', true);
$pdf->Cell(2, 0.5, 'VL. DOAÇÃO', 1, 0, 'C', true);
$pdf->Cell(2.5, 0.5, 'TOT. DOAÇÕES', 1, 1, 'C', true);

$pdf->SetFont('arial','', 8);

//seleciona os doadores
extract($_POST);
$where = '';
if ($ativo) {
	$where .= " and tb_doador.ativo='$ativo' ";
}

if ($cdtpdoador) {
	$where .= " and tb_doador.cdtpdoador='$cdtpdoador' ";
}

$sql = "select
			tb_doador.*,
			tb_tpdoador.sgtpdoador
		from 
			tb_doador
			inner join tb_tpdoador on tb_doador.cdtpdoador = tb_tpdoador.cdtpdoador
		where 1=1 $where
		order by tb_doador.nmresponsavel";
$sql = mysql_query($sql, $con);

while ($res = mysql_fetch_assoc($sql)) {
	$pdf->SetFillColor(235, 235, 235);
	$pdf->Cell(1, 0.5, $res['cddoador'], 1, 0, 'C');
	$pdf->Cell(7, 0.5, $res['nmresponsavel'], 1, 0, 'L');
	$pdf->Cell(7, 0.5, $res['nmfantasia'], 1, 0, 'L');

	$telefone2 = $res['telefone2'] != '' ? ' / ' . $res['telefone2'] : '' ;
	$telefone3 = $res['telefone3'] != '' ? ' / ' . $res['telefone3'] : '' ;

	$pdf->Cell(7, 0.5, $res['telefone1'] . $telefone2 . $telefone3, 1, 0, 'C');
	$pdf->Cell(1, 0.5, sim_nao($res['ativo']), 1, 0, 'C');
	$pdf->Cell(1, 0.5, $res['sgtpdoador'], 1, 0, 'C');

	$vldoacao = $res['vldoacao'] > 0 ? 'R$' . number_show($res['vldoacao']) : '-' ;
	$pdf->Cell(2, 0.5, $vldoacao, 1, 0, 'C');
	$pdf->Cell(2.5, 0.5, 'R$' . number_show($res['vltotdoacao']), 1, 1, 'C', true);
}

$pdf->SetFont('arial','B', 8);
$pdf->SetFillColor(199, 199, 199);
$pdf->Cell(1, 0.5, 'COD.', 1, 0, 'C', true);
$pdf->Cell(7, 0.5, 'RESPONSÁVEL', 1, 0, 'C', true);
$pdf->Cell(7, 0.5, 'NOME FANTASIA', 1, 0, 'C', true);
$pdf->Cell(7, 0.5, 'TELEFONE(S)', 1, 0, 'C', true);
$pdf->Cell(1, 0.5, 'ATIVO', 1, 0, 'C', true);
$pdf->Cell(1, 0.5, 'TIPO', 1, 0, 'C', true);
$pdf->Cell(2, 0.5, 'VL. DOAÇÃO', 1, 0, 'C', true);
$pdf->Cell(2.5, 0.5, 'TOT. DOAÇÕES', 1, 1, 'C', true);

$pdf->Output();