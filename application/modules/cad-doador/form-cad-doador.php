<?php
/**
 * Formulário de cadastro de empresa
 * - Criado em 06/01/2010
 * 
 * @author Niury Martins - http://niurymartins.com.br
 */

$array_include_arq = array(
array('type' => 'text/css', 'name' => 'jquery/ui.all.css'),
array('type' => 'text/css', 'name' => 'jquery/jquery.autocomplete.css'),
array('type' => 'text/javascript', 'name' => 'ui.core.js'),
array('type' => 'text/javascript', 'name' => 'ui.datepicker.js'),
array('type' => 'text/javascript', 'name' => 'my.datepicker.js'),
array('type' => 'text/javascript', 'name' => 'jquery.maskedinput-1.2.1.js'),
array('type' => 'text/javascript', 'name' => 'masks.js'),
array('type' => 'text/javascript', 'name' => 'ajax.js'),
array('type' => 'text/javascript', 'name' => 'funcoes.js'),
array('type' => 'text/css', 'name' => 'colorbox.css'),
array('type' => 'text/javascript', 'name' => 'jquery.colorbox.js'),
array('type' => 'text/javascript', 'name' => 'colorbox.js'),
array('type' => 'text/javascript', 'name' => 'jquery.maskMoney.0.2.js'),
array('type' => 'text/javascript', 'name' => 'money-masks.js'),
array('type' => 'text/javascript', 'name' => 'jquery.autocomplete.js')
);

require(INCLUDES . '/inc.header.php');

//destroi a sessao de controle de pag
if (isset($_SESSION['pagdoacao'])) {
	unset($_SESSION['pagdoacao'], $_SESSION['cddoador']);
}
?>

<script type="text/javascript">
$('document').ready (function() {
	$('#cdtppessoa').change(function() {
		if ($('#cdtppessoa').val() == 2) {
			$('#divjuridico').css('display', 'block');
			$('#divfisico').css('display', 'none');
		}
		else {
			$('#divjuridico').css('display', 'none');
			$('#divfisico').css('display', 'block');
		}
	});
});
</script>

<div class="nav">
	> <a href="<?php echo montalink('cadastros');?>">Cadastros</a>
	> <a href="<?php echo montalink('lista-doador') ?>">Doadores</a>
	> Cadastrar doador
</div>

<?php
if (isset($_POST['nmresp'])) {
	$_POST['nmresp'] = trim($_POST['nmresp']);

	if ($_POST['nmresp'] == '') {
		$nmresp = '';
	}
	else {
		$nmresp = $_POST['nmresp'];
		$nmresp = split(' -- ', $nmresp);
		$_REQUEST['cddoador'] = $nmresp[1];
	}
}

if ((int)$_REQUEST['cddoador']) {
	$cddoador = (int)$_REQUEST['cddoador'];
}

//mostra_array($_SESSION);
if (isset($_SESSION['msg']['caddoador'])) {
	echo show_session_msg('caddoador');
	destroy_session_msg('caddoador');
}

//verifica se existe o cod e busca os dados do banco
if ($cddoador) {
	$sql = "select
				tb_doador.*,
				tb_cidade.*
			from 
				tb_doador, 
				tb_cidade
			where 
				tb_doador.cddoador = '$cddoador' and
				tb_doador.cdcidade = tb_cidade.cdcidade";
	$sql = mysql_query($sql, $con);
	if (mysql_num_rows($sql)) {
		$sql = mysql_fetch_assoc($sql);
		array_walk($sql, 'array_var_show');
		//mostra_array($sql);
		extract($sql);

		if ($dtniverresp) {
			$dtniverresp = inverte_formato_data($dtniverresp, '/');
		}

		if ($dtniverconjuge) {
			$dtniverconjuge = inverte_formato_data($dtniverconjuge, '/');
		}

		$nmresp = $nmresponsavel . ' -- ' . $cddoador;
	}
}

//verifica se existe a sessão para exibir dados
if (isset($_SESSION['postcaddoador'])) {
	array_walk($_SESSION['postcaddoador'], 'array_var_show');
	extract($_SESSION['postcaddoador']);
	unset($_SESSION['postcaddoador']);
}

//verifica se foi selecionado o tipo de empresa
if ($_REQUEST['cdtppessoa']) {
	$cdtpempresa = $_REQUEST['cdtppessoa'];
}

//caso não exista empresa seleciona coloca o tipo contato como padrão
if (!$tppessoa) {
	$res['tppessoa'] = 'F';
	$tppessoa = 'F';
}
?>
<?php 
/*
echo label('PESQUISA'); ?>
<form method="post" action="<?php echo montalink('form-cad-doador'); ?>">
<select name="cddoador" id="cddoador" style="width:350px;" onchange="this.form.submit();">
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
</form>
<br />
*/

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
});
//]]>
</script>
<form method="post" action="<?php echo montalink('form-cad-doador'); ?>">
	<p>
		<?php echo label('Responsável', 'nmresp', false, 'width:140px;'); ?>
		<input type="text" name="nmresp" onfocus="this.value = '';" value="<?php echo $nmresp; ?>" style="width:300px;" id="nmresp" />
		<input type="submit" name="pesquisar" value="Ok" style="width:50px;" class="botao" />
	</p>
</form>
<br />


<div style="border:#CCC 1px solid; width:1200px; margin:0 auto;">
	<form method="post" class="form" style="width:590px; border:none; float:left;" action="<?php echo $action = $cddoador ? montalink('script-update-doador') : montalink('script-insert-doador') ; ?>">
		<p class="navbar">
			<input type="button" onclick="window.location.href='<?php echo montalink('form-cad-doador');?>';" value="NOVO" name="novo" class="botao" />
			<input type="submit" value="SALVAR" name="salvar" class="botao" />
			<input type="button" value="CANCELAR" onclick="window.location.href='<?php echo montalink('lista-doador'); ?>'" name="cancelar" class="botao" />
			<?php $disabled = !$cddoador ? ' disabled="disabled" ' : '' ; ?>
			<input type="button" <?php echo $disabled; ?> value="EXCLUIR" onclick="if(confirma(0)) window.location.href='<?php echo montalink('script-delete-doador'); ?>&amp;pag=form-cad-doador&amp;cddoador=<?php echo $cddoador; ?>'; else return false;" name="excluir" class="botao" />
		</p>	
		<div>
			<?php 
			if ($cddoador) {
				echo '<input type="hidden" name="cddoador" id="cddoador" value="' , $cddoador , '" />';
			}

			echo label('CÓDIGO', 'coddoador', false, 'width:130px;');
			?>
			<input type="text" name="coddoadorv" id="coddoadorv" value="<?php echo $cddoador; ?>" style="width:90px;" disabled="disabled" />
			<span class="dscampo">(O código do doador será preenchido automaticamente ao salvar)</span>
			<br />
			
			<?php echo label('Ativo', 'ativo', true, 'width:130px;') ?>
			<select name="ativo" id="ativo" style="width:100px;border-color:#038d00;background:#d4ffd3;">
				<option value="S">Sim</option>
				<?php $selected = ($ativo == 'N') ? 'selected="selected"' : '' ; ?>
				<option value="N" <?php echo $selected; ?>>Não</option>
			</select>
			<br />
			
			<?php		
			echo label('PESSOA', 'cdtppessoa', true, 'width:130px;');
			//$disabled = ($sgtpempresa == 'CL' and $cdempresa) ? 'disabled="disabled"' : '' ;
			?>
			<select name="cdtppessoa" id="cdtppessoa" style="width:100px;">
				<?php 
				$sql = "select *
						from tb_tppessoa
						order by nmtppessoa";
				$sql = mysql_query($sql, $con);
				while ($res = mysql_fetch_assoc($sql)) {
					$selected = $res['cdtppessoa'] == $cdtppessoa ? 'selected="selected"' : '' ;
					echo '<option ' , $selected , ' value="' , $res['cdtppessoa'] , '">' , $res['nmtppessoa'] , '</option>';
				}
				?>
			</select>
			<br />
			
				<?php 
				if ($cdtppessoa == 2) {
					$style = "display:block;";
				}
				else {
					$style = 'display:none;';
				}
				?>
				<div id="divjuridico" style="<?php echo $style; ?>">
					<?php
					echo label('CNPJ', 'cnpj', false, 'width:130px;'); ?>
					<input type="text" name="cnpj" class="mask-cnpj" value="<?php echo $cnpj;?>" id="cnpj" style="width:200px;" maxlength="100" />
					<br />
					
					<?php echo label('RAZÃO SOCIAL', 'razaosocial', false, 'width:130px;'); ?>
					<input type="text" name="razaosocial" id="razaosocial" value="<?php echo $razaosocial; ?>" style="width:300px;" />
					<br />
					
					<?php 
					echo label('NOME FANTASIA', 'nmfantasia', false, 'width:130px;'); ?>
					<input type="text" name="nmfantasia" value="<?php echo $nmfantasia;?>" id="nmfantasia" style="width:300px;" maxlength="100" />
					<br />
				</div>

				<?php 
				if ($cdtppessoa == 1 or !$cdtppessoa) {
					$style = "display:block;";
				}
				else {
					$style = "display:none;";
				}
				?>
				<div id="divfisico" style="<?php echo $style; ?>">
					<?php
					echo label('CPF', 'cpf', false, 'width:130px;'); ?>
					<input type="text" name="cpf" class="mask-cpf" value="<?php echo $cpf;?>" id="cpf" style="width:300px;" maxlength="100" />
					<br />
				</div>
			
				<?php echo label('RESPONSÁVEL', 'nmresponsavel', true, 'width:130px;'); ?>
				<input type="text" name="nmresponsavel" value="<?php echo $nmresponsavel; ?>" id="nmresponsavel" style="border-color:#038d00;background:#d4ffd3;width:250px;" maxlength="100" />
				
				<?php echo label('DT. NIVER', 'dtniverresp', false, 'float:none;'); ?>
				<input type="text" name="dtniverresp" id="dtniverresp" value="<?php echo $dtniverresp; ?>" style="width:80px;" class="datepicker" />
				<br />
				
				<?php echo label('CÔNJUGE', 'nmconjuge', false, 'width:130px;'); ?>
				<input type="text" name="nmconjuge" value="<?php echo $nmconjuge; ?>" id="nmconjuge" style="width:250px;" maxlength="100" />
				
				<?php echo label('DT. NIVER', 'dtniverconjuge', false, 'float:none;'); ?>
				<input type="text" name="dtniverconjuge" id="dtniverconjuge" value="<?php echo $dtniverconjuge; ?>" style="width:80px;" class="datepicker" />
				<br />
				<br />
				
				<?php echo label('Estado', 'cdestado', true, 'width:130px;'); ?>
				<select name="cdestado" onchange="carregar_cidades(this.value, 'cdcidade');" id="cdestado" style="width:50px;">
					<option value="0">-</option>
					<?php 
					$sql = "select distinct tb_estado.*
							from tb_estado, tb_cidade
							where tb_estado.cdestado = tb_cidade.cdestado $where
							order by tb_estado.sgestado";
					$sql = mysql_query($sql, $con);
					$x = 0;
					while ($res = mysql_fetch_assoc($sql)) {
						$class = ++$x % 2 != 0 ? 'lista_par' : 'lista_impar' ;
						$selected = $cdestado == $res['cdestado'] ? ' selected="selected" ' : '' ;
						echo '<option ' , $selected , ' class="' , $class , '" value="' , $res['cdestado'] , '">' , $res['sgestado'] , '</option>';
					}
					?>
				</select>
				
				<?php echo label('Cidade', 'cdcidade', true, 'float:none;'); ?>
				<select name="cdcidade" id="cdcidade" style="width:193px;border-color:#038d00;background:#d4ffd3;">
					<?php
					if ($cdestado) {
						echo '<option value="0">' , SELECIONE , '</option>';
						$sql = "select tb_cidade.*
								from tb_cidade 
								where tb_cidade.cdestado = '$cdestado' $where 
								order by tb_cidade.nmcidade";
						$sql = mysql_query($sql, $con);
						while ($res = mysql_fetch_assoc($sql)) {
							$class = ++$x % 2 != 0 ? 'lista_par' : 'lista_impar' ;
							$selected = $cdcidade == $res['cdcidade'] ? ' selected="selected" ' : '' ;
							echo '<option ' , $selected , ' class="' , $class , '" value="' , $res['cdcidade'] , '">' , $res['nmcidade'] , '</option>';
						}
					}
					else {
						echo '<option value="0">:.Selecione o estado.:</option>';
					}
					?>
				</select>
				<br />
				
				<?php echo label('Bairro', 'cdbairro', true, 'width:130px;'); ?>
				<input name="nmbairro" id="nmbairro" value="<?php echo $nmbairro; ?>" style="width:300px;border-color:#038d00;background:#d4ffd3;" maxlength="100" />
				<br />
				
				<?php echo label('endereco', 'endereco', true, 'width:130px;'); ?>
				<input type="text" name="endereco" value="<?php echo $endereco; ?>" id="endereco" style="width:300px;border-color:#038d00;background:#d4ffd3;" maxlength="100" />
				<br />
				
				<?php 
				echo label('CEP', 'cep', false, 'width:130px;'); ?>
				<input type="text" name="cep" id="cep" value="<?php echo $cep;?>" style="width:100px;" class="mask-cep" maxlength="14" />
				<br />
				<br />
				
				<?php echo label('E-mail', 'email', false, 'width:130px;');?>
				<input type="text" value="<?php echo $email;?>" name="email" id="email" style="width:300px;" maxlength="100" />
				<br />
				
				<?php echo label('Telefones', 'telefone1', false, 'width:130px;');?>
				<input type="text" maxlength="15" value="<?php echo $telefone1;?>" class="mask-fone" name="telefone1" id="telefone1" style="width:90px;border-color:#038d00;background:#d4ffd3;" />
				<input type="text" maxlength="15" value="<?php echo $telefone2;?>" class="mask-fone" name="telefone2" id="telefone2" style="width:90px;border-color:#038d00;background:#d4ffd3;" />
				<input type="text" maxlength="15" value="<?php echo $telefone3;?>" class="mask-fone" name="telefone3" id="telefone3" style="width:90px;border-color:#038d00;background:#d4ffd3;" />
				<br />
				<br />
			</div>
			
			<p>
				<?php echo label('TIPO DE DOADOR', 'cdtpdoador', true, 'width:130px;') ?>
				<select name="cdtpdoador" id="cdtpdoador" style="width:326px;">
					<option value="0"><?php echo SELECIONE; ?></option>
					<?php
					$sql = "select *
							from tb_tpdoador
							order by sgtpdoador";
					$sql = mysql_query($sql, $con);
					while ($res = mysql_fetch_assoc($sql)) {
						$selected = $cdtpdoador == $res['cdtpdoador'] ? 'selected="selected"' : '' ;
						echo '<option ' , $selected , ' value="' , $res['cdtpdoador'] , '">' , $res['sgtpdoador'] , ' (' , $res['dstpdoador'] , ')</option>';
					}
					?>
				</select>
				<br />
				
				<?php echo label('DIA P/ RECEBIMENTO', 'diarec', false, 'width:130px;') ?>
				<input type="text" name="diarec" id="diarec" value="<?php echo $diarec; ?>" style="width:90px;" onkeypress="return sonumero(event);" />
				
				<?php echo label('VALOR DE DOAÇÃO', 'vldoacao', false, 'float:none;') ?>
				<input type="text" name="vldoacao" id="vldoacao" value="<?php echo number_show($vldoacao); ?>" style="width:90px;" class="mask-real" />
				<br />
				
				<?php echo label('OPERADOR', 'cdusuario', false, 'width:130px;'); ?>
				<select name="cdusuario" id="cdusuario" style="width:326px;border-color:#038d00;background:#d4ffd3;">
					<option value="0"><?php echo SELECIONE; ?></option>
					<?php
					$sql = "select distinct sis_usuario.cdusuario, sis_usuario.nmusuario
							from 
								sis_usuario,
								sis_usuario_tpusuario,
								sis_tpusuario
							where
								sis_usuario.cdusuario = sis_usuario_tpusuario.cdusuario and
								sis_usuario_tpusuario.cdtpusuario = sis_tpusuario.cdtpusuario and
								sis_tpusuario.sgtpusuario = 'OP'
							order by 
								sis_usuario.nmusuario";
					$sql = mysql_query($sql, $con);
					while ($res = mysql_fetch_assoc($sql)) {
						$selected = $cdusuario == $res['cdusuario'] ? 'selected="selected"' : '' ;
						echo '<option ' , $selected , ' value="' , $res['cdusuario'] , '">' , $res['cdusuario'] , ' - ' , $res['nmusuario'] , '</option>';
					}
					?>
				<select>
			</p>
			<p>
				<?php echo label('Observações (P/ controle interno)', 'obsdoador', false, 'width:130px;') ?>
				<textarea name="obsdoador" id="obsdoador" style="width:400px; height:50px;" cols="20" rows="10"><?php echo $obsdoador; ?></textarea>
			</p>
			
			<p>
				<?php echo label('Observações p/ impressão no recibo', 'obsrecibo', false, 'width:130px;') ?>
				<textarea name="obsrecibo" id="obsrecibo" style="width:400px; height:50px;" cols="20" rows="10"><?php echo $obsrecibo; ?></textarea>
			</p>
			<div class="clear"></div>
		<p class="navbar" style="margin:20px 0 0 0;">
			<input type="button" onclick="window.location.href='<?php echo montalink('form-cad-doador');?>';" value="NOVO" name="novo" class="botao" />
			<input type="submit" value="SALVAR" name="salvar" class="botao" />
			<input type="button" value="CANCELAR" onclick="window.location.href='<?php echo montalink('lista-doador'); ?>'" name="cancelar" class="botao" />
			<?php $disabled = !$cdempresa ? ' disabled="disabled" ' : '' ; ?>
			<input type="button" <?php echo $disabled; ?> value="EXCLUIR" onclick="if(confirma(0)) window.location.href='<?php echo montalink('script-delete-doador'); ?>&amp;pag=form-cad-doador&amp;cddoador=<?php echo $cddoador; ?>'; else return false;" name="excluir" class="botao" />
		</p>	
	</form>
	
	<div style="width:570px; float:right; padding:0; height:400px; margin-left:10px; background:#f9f9f9;">
		<?php require('inc.form-lista-doacoes.php') ?>
	</div>
	<div class="clear"></div>
</div>

<?php
//require('inc.form-cad-foto-empresa.php');
require(INCLUDES . '/inc.footer.php');