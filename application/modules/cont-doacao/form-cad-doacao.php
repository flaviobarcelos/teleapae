<?php
/**
 * Formulário de cadastro de doação
 * 
 */

$array_include_arq = array(
array('type' => 'text/css', 'name' => 'jquery/ui.all.css'),
array('type' => 'text/css', 'name' => 'jquery/jquery.autocomplete.css'),
array('type' => 'text/javascript', 'name' => 'ui.core.js'),
array('type' => 'text/javascript', 'name' => 'ui.datepicker.js'),
array('type' => 'text/javascript', 'name' => 'my.datepicker.js'),
array('type' => 'text/javascript', 'name' => 'jquery.maskedinput-1.2.1.js'),
array('type' => 'text/javascript', 'name' => 'masks.js'),
array('type' => 'text/javascript', 'name' => 'funcoes.js'),
array('type' => 'text/javascript', 'name' => 'jquery.maskMoney.0.2.js'),
array('type' => 'text/javascript', 'name' => 'money-masks.js'),
array('type' => 'text/javascript', 'name' => 'jquery.autocomplete.js')
);

require(INCLUDES . '/inc.header.php');

$cddoacao = (int)$_GET['cddoacao'];

if ((int)$_SESSION['cddoador']) {
	$cddoador = (int)$_SESSION['cddoador'];
}
else if ((int)$_GET['cddoador']) {
	$cddoador = (int)$_GET['cddoador'];
}

//caso exista cod doacao
if ($cddoacao) {
	$sql = "select *
			from tb_doacao
			where cddoacao = '$cddoacao'";
	$sql = mysql_query($sql, $con);
	if (mysql_num_rows($sql)) {
		$sql = mysql_fetch_assoc($sql);
		extract($sql);
		if ($dtcontato) {
			$dtcontato = inverte_formato_data($dtcontato, '/');
		}

		if ($dtrec) {
			$dtrec = inverte_formato_data($dtrec, '/');
		}

		$vldoacao = number_show($vldoacao);
	}
}

//tratamento para o nome do responsavel selecionado
if ($_SESSION['postcaddoacao']['nmresp']) {
	$array = split(' -- ', $_SESSION['postcaddoacao']['nmresp']);
	$cddoador = $array[1];
	$nmresp = $array[0];
}

//caso exista o código do doador
if ($cddoador) {
	$sql = "select
				cddoador,
				obsrecibo as obsrecdoacao,
				vldoacao,
				diarec
			from tb_doador
			where cddoador = '$cddoador'";
	$sql = mysql_query($sql, $con);
	if (mysql_num_rows($sql)) {
		$sql = mysql_fetch_assoc($sql);
		array_walk($sql, 'array_var_show');
		extract($sql);

		if ($vldoacao) {
			$vldoacao = number_show($vldoacao);
		}

		if ($diarec and !$dtrec) {
			if ($diarec >= 1 and $diarec <= 9 and strlen($diarec) <= 1) {
				$diarec = '0' . $diarec;
			}

			$dtrec = $diarec . '/' . date('m/Y');
		}
	}
}

//caso exista post
if (isset($_SESSION['postcaddoacao'])) {
	extract($_SESSION['postcaddoacao']);
	unset($_SESSION['postcaddoacao']);
}

//tratamento pag retorno
if (!isset($_SESSION['pagdoacao'])) {
	if (isset($_GET['pag'])) {
		$_SESSION['pagdoacao'] = $_GET['pag'];
		$_SESSION['cddoador'] = $cddoador;
	}
	else {
		$_SESSION['pagdoacao'] = 'lista-doacao';
	}
}

//echo $_SESSION['pagdoacao'];

?>

<div class="nav">
	> <a href="<?php echo montalink('cadastros'); ?>">Cadastros</a>
	<?php 
	if ($_SESSION['cddoador']) {
		?>
		> <a href="<?php echo montalink('lista-doador'); ?>">Doadores</a>
		> <a href="<?php echo montalink('form-cad-doador'); ?>&amp;cddoador=<?php echo $_SESSION['cddoador']; ?>">Cadastrar doador</a>
		<?php
	}
	else {
		?>
		> <a href="<?php echo montalink('lista-doacao'); ?>">Doações</a>
		<?php
	}
	?>
	
	> Cadastrar doação
</div>

<?php
//caso exista mensagem
if (isset($_SESSION['msg']['caddoacao'])) {
	echo show_session_msg('caddoacao');
	destroy_session_msg('caddoacao');
}
?>

<script type="text/javascript">
//<![CDATA[
$('document').ready(function() {
	function formatItem(row) {
		var nome = row[0].split(" -- ");
		return nome[0];
	}

	function formatResult(row) {
		var nome = row[0].split(" -- ");
		//$('#cddoadorx').val(nome[1]);
		return nome[0];
	}

	function formatMatch(row) {
		var nome = row[0].split(" -- ");
		//$('#cddoadorx').val(nome[1]);
		return nome[0];
	}

	$("#nmresp").autocomplete("<?php echo SITE_URL , '/' , montalink('ajax', '&'); ?>&getdoadorbynome=true", {
		width: 400,
		autoFill: true,
		matchContains: true,
		//matchContains: "word",
		mustMatch: false,
		minChars: 3,
		max: 255,
		scrollHeight: 250,
		highlight: false,
		selectFirst: true/*,
		formatItem: formatItem,
		formatResult: formatResult,
		formatMatch: formatMatch

		formatItem: function(data) {
		//var dados = data.split(" -- ");
		//return dados[0];
		//return data;
		},
		formatResult: function(data) {
		return data.name;
		}*/
	});

	$("#nmresp").blur(function() {
		$.ajax({
			type: "POST",
			url: "<?php echo SITE_URL , '/' , montalink('ajax', '&'); ?>",
			data: "getDoadorJsonByNome=true&nmresp=" + $('#nmresp').val(),
			beforeSend: function() {
				//$('#obsrecdoacao').val('Carregando...');
			},
			success: function(txt) {
				var obj = jQuery.parseJSON(txt);
				//if ($('#obsrecdoacao').val() == '') {
				$('#obsrecdoacao').val(obj.obsrecibo);
				//}

				if ($('#vldoacao').val() == '') {
					$('#vldoacao').val(obj.vldoacao);
				}

				if ($('#dtrec').val() == '') {
					$('#dtrec').val(obj.dtrec);
				}
			},
			error: function(txt) {
				alert('Desculpe, houve um erro interno.');
			}
		});
	});
});
//]]>
</script>

<form method="post" class="form" action="<?php echo $cddoacao ? montalink('script-update-doacao') : montalink('script-insert-doacao') ; ?>">
	<p class="navbar" style="margin:0 0 20px 0;">
		<input type="button" onclick="window.location.href='<?php echo montalink('form-cad-doacao');?>';" value="NOVO" name="novo" class="botao" />
		<input type="submit" value="SALVAR" name="salvar" class="botao" />
		<input type="button" value="CANCELAR" onclick="window.location.href='<?php echo montalink($_SESSION['pagdoacao']); if ($_SESSION['cddoador']) { echo '&amp;cddoador=' . $_SESSION['cddoador']; } ?>'" name="cancelar" class="botao" />
		<?php $disabled = !$cddoacao ? ' disabled="disabled" ' : '' ; ?>
		<input type="button" <?php echo $disabled; ?> value="EXCLUIR" onclick="if(confirma(0)) window.location.href='<?php echo montalink('script-delete-doacao'); ?>&amp;pag=form-cad-doacao&amp;cddoacao=<?php echo $cddoacao; ?>'; else return false;" name="excluir" class="botao" />
	</p>
	<p>
		<?php 
		echo label('COD.', 'textcddoacao');

		if ($cddoacao) {
			echo '<input type="hidden" value="' , $cddoacao , '" name="cddoacao" />';
		}
		?>
		<input type="text" name="textcddoacao" id="textcddoacao" value="<?php echo $cddoacao; ?>" disabled="disabled" style="width:100px;" />
		<span class="dscampo">(SERÁ PREENCHIDO AUTOMATICAMENTE AO SALVAR)</span>
		<br />
		
		<?php 
		echo label('DOADOR', 'cddoador', true);
		/*
		if (!isset($_SESSION['cddoador'])) {

		?>
		<select name="cddoador" id="cddoador" style="width:350px;">
		<option value="0"><?php echo SELECIONE; ?></option>
		<?php
		$sql = "select cddoador, nmresponsavel
		from tb_doador
		order by nmresponsavel";
		$sql = mysql_query($sql, $con);
		while ($res = mysql_fetch_assoc($sql)) {
		$selected = $cddoador == $res['cddoador'] ? 'selected="selected"' : '' ;
		echo '<option ' , $selected , ' value="' , $res['cddoador'] , '">' , $res['nmresponsavel'] , '</option>';
		}
		?>
		</select>
		<?php

		if ($cddoador) {
		$sql = "";
		}
		?>
		<input type="text" name="nmresp" onfocus="this.value = '';" value="<?php echo $nmresp; ?>" style="width:400px;" id="nmresp" />
		<?php
		}
		*/

		$disabled = '';
		$nmresp = '';
		if ($cddoador or $_SESSION['cddoador']) {
			if ($_SESSION['cddoador']) {
				$cddoador = $_SESSION['cddoador'];
				$disabled = 'disabled="disabled"';
			}
			else {
				$disabled = '';
			}

			$sql = "select
						cddoador,
						nmresponsavel 
					from
						tb_doador 
					where 
						cddoador = '$cddoador'";
			$sql = mysql_query($sql, $con);
			$sql = mysql_fetch_assoc($sql);
			$nmresp = $sql['nmresponsavel'] . ' -- ' . $sql['cddoador'];
			echo '<input type="hidden" name="cddoador" id= "cddoador" value="',$sql['cddoador'],'"  />';
		}
		echo '<input type="text" id="nmresp" name="nmresp" ' , $disabled , ' value="',$nmresp,'" style="width:400px;" />';
		?>
		<br />
		
		<?php echo label('DT. CONTATO', 'dtcontato', false); ?>
		<input type="text" name="dtcontato" id="dtcontato" value="<?php echo $dtcontato; ?>" maxlength="20" style="width:100px;" class="datepicker" />
		<br />
		
		<?php echo label('DT. RECEBIMENTO', 'dtrec', true); ?>
		<input type="text" name="dtrec" id="dtrec" value="<?php echo $dtrec; ?>" style="width:100px;" maxlength="20" class="datepicker" />
		<br />
		
		<?php echo label('VALOR', 'vldoacao', true); ?>
		<input type="text" name="vldoacao" id="vldoacao" value="<?php echo $vldoacao; ?>" style="width:100px;" maxlength="14" class="mask-real" />
		<br />
		
		<?php echo label('CANCELADO', 'cancelado', false); ?>
		<select name="cancelado" id="cancelado" style="width:108px;">
			<option value="N">NÃO</option>
			<?php $selected = $cancelado == 'S' ? 'selected="selected"' : '' ; ?>
			<option value="S" <?php echo $selected; ?>>SIM</option>	
		</select>
	</p>
	<p>
		<?php echo label('OBSERVAÇÕES PARA CONTROLE INTERNO', 'obsdoacao', false); ?>
		<textarea name="obsdoacao" id="obsdoacao" cols="70" rows="5" style="width:400px; height:100px;"><?php echo $obsdoacao; ?></textarea>
	</p>
	<p>
		<?php echo label('OBSERVAÇÕES P/ IMPRESSÃO NO RECIBO', 'obsrecdoacao', false); ?>
		<textarea name="obsrecdoacao" id="obsrecdoacao" cols="70" rows="5" disabled="disabled" style="width:400px; height:100px;"><?php echo $obsrecdoacao; ?></textarea>
	</p>
	<p>
		<label>&nbsp;</label><span class="dscampo">A mensagem deste campo será impressa somente no recibo em questão</span>
	</p>
	<p class="navbar" style="margin:20px 0 0 0;">
		<input type="button" onclick="window.location.href='<?php echo montalink('form-cad-doacao');?>';" value="NOVO" name="novo" class="botao" />
		<input type="submit" value="SALVAR" name="salvar" class="botao" />
		<input type="button" value="CANCELAR" onclick="window.location.href='<?php echo montalink($_SESSION['pagdoacao']); ?>'" name="cancelar" class="botao" />
		<?php $disabled = !$cddoacao ? ' disabled="disabled" ' : '' ; ?>
		<input type="button" <?php echo $disabled; ?> value="EXCLUIR" onclick="if(confirma(0)) window.location.href='<?php echo montalink('script-delete-doacao'); ?>&amp;pag=form-cad-doacao&amp;cddoacao=<?php echo $cddoacao; ?>'; else return false;" name="excluir" class="botao" />
	</p>
</form>

<?php
require(INCLUDES . '/inc.footer.php');