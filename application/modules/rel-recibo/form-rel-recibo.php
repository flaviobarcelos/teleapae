<?php
/**
 * Formul�rio de fitro para impress�o dos recibos
 */

$array_include_arq = array(
array('type' => 'text/css', 'name' => 'jquery/ui.all.css'),
array('type' => 'text/javascript', 'name' => 'ui.core.js'),
array('type' => 'text/javascript', 'name' => 'ui.datepicker.js'),
array('type' => 'text/javascript', 'name' => 'my.datepicker.js'),
array('type' => 'text/javascript', 'name' => 'jquery.maskedinput-1.2.1.js'),
array('type' => 'text/javascript', 'name' => 'masks.js'),
array('type' => 'text/javascript', 'name' => 'ajax.js'),
array('type' => 'text/javascript', 'name' => 'funcoes.js'),
array('type' => 'text/javascript', 'name' => 'tiny_mce/tiny_mce.js'),
array('type' => 'text/javascript', 'name' => 'tiny_mce/my_tiny_mce.js')
);

require(INCLUDES . '/inc.header.php');
?>

<div class="nav">
	> <a href="<?php echo montalink('relatorios'); ?>">Relat�rios</a>
	> Impress�o de recibos
</div>

<form class="form" style="width:500px;" target="_blank" method="post" action="<?php echo montalink('rel-recibo-economico'); ?>" class="form">
	<p>
		<?php echo label('COD.', 'cddoacao', false); ?>
		<input type="text" style="width:250px;" name="cddoacao" id="cddoacao" value="" />
		<span class="dscampo">Ex: 132, 5423, 989</span>
		<br />

		<?php
		/*
		echo label('DOADOR', 'cddoador', false); ?>
		<select name="cddoador" id="cddoador" style="width:300px;">
			<option value="0"><?php echo SELECIONE; ?></option>
			<?php
			$sql = "select cddoador, nmresponsavel
					from tb_doador
					order by nmresponsavel";
			$sql = mysql_query($sql, $con);
			while ($res = mysql_fetch_assoc($sql)) {
				echo '<option value="' , $res['cddoador'] , '">' , $res['nmresponsavel'] , '</option>';
			}
			?>
		</select>
		*/?>

		<?php echo label('DT. RECEBIMENTO', 'dtrecini', true); ?>
		<input name="dtrecini" id="dtrecini" value="" style="width:100px;" maxlength="20" class="datepicker" />
		&nbsp;a&nbsp;
		<input name="dtrecfim" id="dtrecfim" value="" style="width:100px;" maxlength="20" class="datepicker" />

		<?php echo label('RECIBO CANCELADO', 'cancelado', false); ?>
		<select name="cancelado" id="cancelado" style="width:109px;">
			<option value="N">N�O</option>
			<option value="S">SIM</option>
			<option value="0">TODOS</option>
		</select>
		<br />

			<script type="text/javascript">
			//<![CDATA[
			$('document').ready( function() {
				$('#cdcidade').change( function() {

					if ($('#cdcidade').val() != 0) {

						$.ajax({
							type: "POST",
							url: "<?php echo SITE_URL , '/' , montalink('ajax', '&'); ?>",
							data: "getbairroscidade=true&cdcidade=" + $('#cdcidade').val(),
							beforeSend: function() {
								$('#bairros').html('Carregando...');
							},
							success: function(txt) {
								$('#bairros').html(txt);
							},
							error: function(txt) {
								alert('Desculpe, houve um erro interno.');
							}
						});
					}
				});
			});
			//]]>
		</script>

		<?php echo label('CIDADE', 'cdcidade', false); ?>
		<select name="cdcidade" id="cdcidade" style="width:300px;">
			<option value="0">TODAS</option>
			<?php
			$sql = "select
						distinct tb_cidade.cdcidade, tb_cidade.nmcidade
					from 
						tb_cidade,
						tb_doador
					where 
						tb_cidade.cdcidade = tb_doador.cdcidade
					order by 
						tb_cidade.nmcidade";
			$sql = mysql_query($sql, $con);
			while ($res = mysql_fetch_assoc($sql)) {
				echo '<option value="' , $res['cdcidade'] , '">' , $res['nmcidade'] , '</option>';
			}
			?>
		</select>

		<div class="clear"></div>
		<?php echo label('BAIRRO', 'cdbairro', false); ?>
		<div style="border:#CCC 1px solid; width:330px; float:left; height:190px; overflow:scroll;" id="bairros">
		</div>
	</p>
	<p>
		<?php echo label('Ordena��o', 'ordenacao', false); ?>
		<select name="ordenacao" id="ordenacao" style="width:300px;">
			<option value="tb_cidade.nmcidade, tb_doador.nmbairro, tb_doador.endereco, tb_doador.num, tb_doador.nmresponsavel">Cidade, Bairro, Endere�o, Num, Respons�vel</option>
			<option value="tb_doacao.cddoacao">C�digo da doa��o</option>
		</select>
	</p>
	<br />
	<br />
	<p style="text-align:center;">
		<input type="submit" name="enviar" value="GERAR RECIBOS" class="botao" style="width:150px;" />
	</p>
</form>

<?php
require(INCLUDES . '/inc.footer.php');