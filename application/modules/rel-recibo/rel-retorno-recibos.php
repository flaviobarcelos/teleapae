<?php
/**
 * Retorno de doações
 * - Criado em 05/07/2017
 * 
 * @author Bruno Portes - http://devsistemas.com.br
 */


$array_include_arq = array(
array('type' => 'text/css', 'name' => 'jquery/ui.all.css'),
array('type' => 'text/javascript', 'name' => 'ui.core.js'),
array('type' => 'text/javascript', 'name' => 'ui.datepicker.js'),
array('type' => 'text/javascript', 'name' => 'my.datepicker.js'),
array('type' => 'text/javascript', 'name' => 'jquery.maskedinput-1.2.1.js'),
array('type' => 'text/javascript', 'name' => 'masks.js'),
array('type' => 'text/javascript', 'name' => 'funcoes.js')
);

require(INCLUDES . '/inc.header.php'); 
require('script-trata-pesquisa-doador.php');
?>
<div class="nav">
	> <a href="<?php echo montalink('cadastros');?>">Relatórios</a>
	> Retorno de doação
</div>
<script>
	function limparInput()
	{
		$('#codigo_barra').val('');
	}
	function buscaCodigoBarra()
	{
		$('#formPesquisaCodigoBarra').submit();
	}
</script>
<fieldset style="width:auto; margin-bottom:30px;">
	<legend>PESQUISA</legend>
	<form id="formPesquisaCodigoBarra"> method="post" action="<?php echo montalink('form-retorno-doacao');?>">
		<p>
			<label for="codigo_barra">Código</label>
			<input type="text" id="codigo_barra" name="codigo_barra" onkeyup="buscaCodigoBarra()">
		</p>
		<p style="text-align:center; margin-top:20px;">
			<input type="submit" name="pesquisar" id="pesquisar" value="Pesquisar" class="botao" />
			<button type="button" class="botao" onclick="limparInput()">Limpar</button>
		</p>
	</form>
</fieldset>

<?php 

//caso exista mensagem para exibir
if (isset($_SESSION['msg']['caddoador'])) {
	echo show_session_msg('caddoador');
	destroy_session_msg('caddoador');
}

if(isset($_POST['codigo_barra']))
{
	//seleciona o total de empresas cadastradas
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
		where tb_doacao.codigo_barra = ".$_POST['codigo_barra']."
		order by
		      tb_cidade.nmcidade,
		      tb_doador.nmbairro,
		      tb_doador.endereco,
		      tb_doador.num,
		      tb_doador.nmresponsavel";

	$qry = mysql_query($qry, $con);
	$res = mysql_fetch_assoc($sql);

	if (empty($res))
	{
		echo msg('information', 'Nenhum doador cadastrado');
		require(INCLUDES . '/inc.footer.php');
		exit();
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

    <input type="radio" name="retornodoacao" value="tentativa"> Tentativa sem êxito<br>
	<input type="radio" name="retornodoacao" value="confirmado"> Doação recebida<br>

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
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="font-size: 9px; font-family: Arial; margin-top: 5px ">
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
	                        $texto = $res['codigo_barra'];
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
                        <!--________ de ___________________ de 20______-->
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php

}
require(INCLUDES . '/inc.footer.php');