<?php
/**
 * impress?o dos recibos
 */

function Recibo($pdf, $res, $x, $ln) {

}

setlocale(LC_ALL, 'pt_BR');
ini_set("memory_limit","1024M");

//tratamento de recebimento dos dados
array_walk_recursive($_POST, 'array_insert_db');
extract($_POST);
$where = '';
$erro = false;
$msg = '';

if (!$dtrecini or !$dtrecfim) {
    $msg = 'Favor informar o per?odo de recebimento';
    $erro = true;
}
else {
    $dtrecini = inverte_formato_data($dtrecini);
    $dtrecfim = inverte_formato_data($dtrecfim);

    if ($dtrecini > $dtrecfim) {
        $msg = 'A data de recebimento inicial n?o pode ser maior que a data final';
        $erro = true;
    }
}

if ($erro) {
    require(INCLUDES . '/inc.header.php');
    echo msg('alert', $msg);
    require(INCLUDES . '/inc.footer.php');
    exit();
}

//realiza a consulta no banco
$where = " tb_doacao.dtrec between '$dtrecini' and '$dtrecfim' ";

if ($cddoacao) {
    $where .= " and tb_doacao.cddoacao in (" . $cddoacao . ") ";
}

if ($cddoador) {
    $where .= " and tb_doacao.cddoador = '$cddoador' ";
}

if ($cancelado) {
    $where .= " and tb_doacao.cancelado = '$cancelado' ";
}

if ($cdcidade) {
    $where .= " and tb_doador.cdcidade = '$cdcidade' ";
}

if (count($nmbairro)) {
    $where .= " and tb_doador.nmbairro in ( ";
    $x = 0;

    foreach ($nmbairro as $res) {
        $where .= $x > 0 ? ',' : '' ;
        $where .= " '$res' ";
        $x++;
    }

    $where .= " ) ";
}

$sql = "select
		       tb_doador.*,
		       tb_doacao.cddoacao,
		       tb_doacao.dtrec,
		       tb_doacao.obsrecdoacao,
		       tb_doacao.vldoacao,
		       tb_tpdoador.*,
		       tb_cidade.nmcidade,
		       tb_estado.sgestado
		from
		    tb_doacao
		    inner join tb_doador on tb_doador.cddoador = tb_doacao.cddoador
		    inner join tb_cidade on tb_doador.cdcidade = tb_cidade.cdcidade
		    inner join tb_estado on tb_cidade.cdestado = tb_estado.cdestado
		    inner join tb_tpdoador on tb_doador.cdtpdoador = tb_tpdoador.cdtpdoador
		    left join sis_usuario on tb_doador.cdusuario = sis_usuario.cdusuario
		where $where
		order by
		      tb_cidade.nmcidade,
		      tb_doador.nmbairro,
		      tb_doador.endereco,
		      tb_doador.num,
		      tb_doador.nmresponsavel";
//die(mostra_array($sql));
$sql = mysql_query($sql, $con);
if (!mysql_num_rows($sql)) {
    require(INCLUDES . '/inc.header.php');
    echo msg('alert', 'Nenhum recibo encontrado');
    require(INCLUDES . '/inc.footer.php');
    exit();
}

include_once(CLASSES . '/pdf/PdfWithBarCode.php');
$pdf = new PdfWithBarCode('P','cm','A4');
$pdf->SetMargins(1.45, 1, 1);
$pdf->AliasNbPages('{total}');
$pdf->SetTitle('RECIBOS DE DOAÇÃO');
$pdf->SetTextColor(00, 00, 00);
$pdf->SetFillColor(200,200,200);

$x = 0;
$img = array(0=>'1.65', 1=>'8.35', 2=>'15.05', 3=>'21.75');

while ($res = mysql_fetch_assoc($sql)) {
    //mostra_array($res);

    for ($w = 0; $w <= 1; $w++) {
        if ($x >= 4) {
            $x = 0;
        }

        if ($x == 0) {
            $pdf->AddPage();
        }


        $pdf->Image(IMG . '/logo_relatorio2.jpg', 1.9, $img[$x], 1.2);
        $pdf->SetFont('arial','B', 10);
        $pdf->Cell(14.5, 0.5, 'ASSOCIAÇÃO DE PAIS E AMIGOS DOS EXCEPCIONAIS DE IPATINGA', 'LTR', 0, 'C', 1);

        $pdf->SetFont('arial','', 8);
        $pdf->Cell(3.5, 0.5, 'RECIBO Nº', 1, 1, 'C', 1);
        $pdf->SetFont('arial','B', 14);
        $pdf->Cell(14.5, 1.5, '', 1, 0, 'R');
        $pdf->Cell(3.5, 1, number_format($res['cddoacao'],'', '.', '.'), 'R', 1, 'C');
        $pdf->Cell(14.5, 0.5, '', 0, 0, 'R');
        $pdf->SetFont('arial','B', 8);
        $pdf->Cell(3.5, 0.5, $res['sgtpdoador'] . ' - ' . $res['cdusuario'], 'RB', 1, 'C');

        $pdf->SetFont('arial', '', 7);

        $pdf->Ln(-1.3);
        $pdf->MultiCell(14, 0.3, '                               Av. 26 de Outubro, 1595, B. Bela Vista, Ipatinga/MG - CEP 35.160-208
                               CNPJ 20.951.190/0001-30 - U.P. MUNICIPAL - LEI Nº 649 de 19/07/79
                               U.P. ESTADUAL - LEI N? 7656 de 27/12/79 - U.P. FEDERAL DECRETO LEI Nº 91.108 de 12/03/85
                               Registro CNSS: 23.002.006747/88-53 - TELEFONE: (31)3822-3502', 0, 1);


        $pdf->SetFont('arial', '', 8);
        $pdf->Ln(0.1);

        //cpf/cnpj
        if ($res['sgtppessoa'] == 'F') {
            $cpf = $res['cpf'];
        }
        else {
            $cpf = $res['cnpj'];
        }

        if (!$cpf) {
            $cpf = '______________________';
        }

        //telefone
        $tel = $res['telefone1'];
        if ($res['telefone2']) {
            $tel .= ' / ' . $res['telefone2'];
        }

        if ($res['telefone3']) {
            $tel .= ' / ' . $res['telefone3'];
        }

        if ($tel) {
            $tel = ', telefone(s) ' . $tel;
        }

        //vldoacao
        if ($res['vldoacao'] <= 0) {
            $vldoacao = '_____________';
        }
        else {
            $vldoacao = 'R$' . number_show($res['vldoacao']);
        }

        $pdf->MultiCell(18, 0.35, '
Recebemos de ' . $res['nmresponsavel'] . ', CNPJ/CPF nº ' . $cpf . ', residente na ' . $res['endereco'] . ', ' . $res['num'] . ', ' . $res['nmbairro'] . ', ' . $res['nmcidade'] . '/' . $res['sgestado'] . $tel . ', a quantia de ' . $vldoacao . ', referente à doação do dia ' . inverte_formato_data($res['dtrec'], '/') . '.

Para clareza e devidos fins, firmamos o presente.
Ipatinga, ' . date('d') . ' de ' . date('F') . ' de ' . date('Y') . '


', 'LR');

        $numero = date(Ymdhis).$res['cddoacao'];
        $barcode = geraCodigoBarra($numero);
        $pdf->SetFont('arial', '', 8);
        $pdf->Cell(9, 0.5, '______________________________________________', 'L', 0, 'C');
        $pdf->Cell(9, 0.5, "$barcode", 'R', 1, 'C');

        $pdf->SetFont('arial', 'B', 8);
        $pdf->Cell(9, 0.5, 'APAE IPATINGA', 'L', 0, 'C');
        //$pdf->Cell(9, 0.5, 'MENSAGEIRO', 'R', 1, 'C');
        $pdf->Cell(9, 0.5, "$numero", 'R', 1, 'C');

        /*
        $pdf->SetFont('arial', '', 7);
        $pdf->Ln(-6.15);
        $pdf->SetX(20);
        $pdf->Rotate(-90);
        $pdf->MultiCell(6.15, 0.3, $res['obsrecibo'], 1, 'L');
        $pdf->Rotate(0);
        $pdf->Ln(6.25);
        */

        $pdf->SetFont('arial', '', 7);
        $pdf->Cell(18, 0.4, $res['obsrecibo'], 'LBR', 1, 'L');

        $pdf->Ln(0.3);
        $x++;
    }
}

$pdf->Output();