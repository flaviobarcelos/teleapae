<?php
echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">';
echo "<link rel='stylesheet' type='text/css' href='" . CSS . "/style.css'>";
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
$texto = '123456789123';
if((strlen($texto) % 2) <> 0){
    $texto = '1'.$texto;
}
$resultado = geraCodigoBarra($texto);
$x=0;
while ($res = mysql_fetch_assoc($sql)) {
    //mostra_array($res);
    //verifica se ja tem 4 formularios em uma folha pra criar o layout da proxima
    if($x % 4 == 0)
    {
        //verifica se ja tem 4 itens pra fechar a div da folha antes de iniciar outra
        if($x>0)echo '</div>';
        echo '<div class="page">';
    }

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

    ?>
    <div class="container-fluid" style="font-weight: bold; margin-top: 5px"
         xmlns:border-right="http://www.w3.org/1999/xhtml">
        <div class="row" style="background-color: #C8C8C8; border-bottom: 1px solid black; ">
            <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8" style="text-align: center; font-size: 10px; font-family: Arial; margin: 5px 0px;">
                ASSOCICAO DE PAIS E AMIGOS DOS EXCEPCIONAIS DE IPATINGA
            </div>
            <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="height: 25px; border-right: solid 1px black">
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3" style=" text-align: center; font-size: 8px; font-family: Arial; padding-top:6px;">
                RECIBO Nº
            </div>
        </div>
        <div class="row" style="border-bottom: 1px solid black;">
            <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                <img width="60px"  src="<?=IMG?>/logo_relatorio2.jpg" alt="">
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="font-size: 8px; font-family: Arial; margin-top: 5px ">
                Av. 26 de Outubro, 1595, B. Bela Vista, Ipatinga/MG - CEP 35.160-208 <br>
                CNPJ 20.951.190/0001-30 - U.P. MUNICIPAL - LEI No 649 de 19/07/79<br>
                U.P. ESTADUAL - LEI No 7656 de 27/12/79 - U.P. FEDERAL DECRETO LEI No 91.108 de 12/03/85<br>
                Registro CNSS: 23.002.006747/88-53 - TELEFONE: (31)3822-3502
            </div>
            <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="height: 70px; border-right: solid 1px black">
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3" style="margin-top:10px; text-align: center; font-weight: bold; font-family: Arial">
                <p style="font-size: 16px"><?php echo $res['cddoacao']; ?></p>
                <p style="font-size: 10px"><?php echo $res['sgtpdoador'] . ' - ' . $res['cdusuario']; ?></p>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style=" margin-top:10px; font-family: Arial; font-size: 10px">
                <p>
                    Recebemos de <?=$res['nmresponsavel']?>, CNPJ/CPF no <?=$cpf?>, residente na <?=$res['endereco']?>, <?=$res['num']?>,
                    <?=$res['nmbairro']?>, <?=$res['nmcidade']?>/<?=$res['sgestado']?>, telefone(s) <?=$tel?>, a quantia de <?=$vldoacao?>, referente à doação do dia <?php inverte_formato_data($res['dtrec'], '/'); ?>.
                </p>
                <br>
                <p>Para clareza e devidos fins, firmamos o presente.</p>
                <p>Ipatinga, <?php echo date('d') . ' de ' . date('F') . ' de ' . date('Y'); ?></p>
                <div class="row" >
                    <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
                    </div>
                    <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5" style="text-align: center">
                        <img width="150px" src="<?=IMG?>/assinatura.png" class="img-responsive" alt="Image">
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="text-align: center">
                        <?php
                        $texto = date(Ymd).$res['cdusuario'];
                        $tam_texto = strlen($texto);
                        while($tam_texto < 12)
                        {
                            $texto.='0';
                            $tam_texto++;
                        }
                        /*if((strlen($texto) % 2) <> 0){
                            $texto = '1'.$texto;
                        }*/
                        $barcode_img = geraCodigoBarra($texto);
                        ?>
                        <br>
                        <?=$barcode_img?><br>
                        <?=$texto?>
                    </div>
                </div>
                <div class="row" style="margin-bottom:5px; font-size: 7px; font-family: Arial;">
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                        <?=$res['obsrecibo']?>
                    </div>
                    <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                        ________ de ___________________ de 20______
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
$x++;
}