<?php
/**
 * Formulário de cadastro de cliente
 * - Criado em 17/01/2011
 * 
 * @author Niury Martins - http://niurymartins.com.br
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

require(INCLUDES . '/inc.header.php'); ?>
<div class="nav">
	> <a href="<?php echo montalink('cadastros');?>">Cadastros</a>
	> <a href="<?php echo montalink('lista-cliente') ?>">Cliente</a>
	> Cadastrar cliente
</div>

<?php
if ((int)$_GET['cdcliente']) {
	$cdcliente = (int)$_GET['cdcliente'];
}

//verifica se o usuário é franquia
if (somenteFranquia()) {
	$where = " and tb_cidade.cdcidade in (" . getCidade() . ")";
}
else {
	$where = '';
}

//mostra_array($_SESSION);
if (isset($_SESSION['msg']['cadcliente'])) {
	echo show_session_msg('cadcliente');
	destroy_session_msg('cadcliente');
}

//verifica se existe o cod e busca os dados do banco
if ($cdcliente) {
	$sql = "select *
			from tb_cliente
			where tb_cliente.cdcliente = '$cdcliente' and
				  tb_cliente.excluido = 'N' $where";
	$sql = mysql_query($sql, $con);
	if (mysql_num_rows($sql)) {
		$sql = mysql_fetch_assoc($sql);
		array_walk($sql, 'array_var_show');
		extract($sql);
		$dtnascimento = inverte_formato_data($dtnascimento, '/');
	}
}

//verifica se existe a sessão para exibir dados
if (isset($_SESSION['postcadcliente'])) {
	extract($_SESSION['postcadcliente']);
	unset($_SESSION['postcadcliente']);
}
?>
<form method="post" class="form" style="width:750px;" action="<?php echo $action = $cdcliente ? montalink('script-update-cliente') : montalink('script-insert-cliente') ; ?>">
	<!--
	<p class="navbar">
		<input type="button" onclick="window.location.href='<?php echo montalink('form-cad-cliente');?>';" value="Novo" name="novo" class="botao" />
		<input type="submit" value="Salvar" name="salvar" class="botao" />
		<input type="button" value="Cancelar" onclick="window.location.href='<?php echo montalink('lista-cliente'); ?>'" name="cancelar" class="botao" />
		<?php $disabled = !$cdcliente ? ' disabled="disabled" ' : '' ; ?>
		<input type="button" <?php echo $disabled; ?> value="Excluir" onclick="if(confirma(0)) window.location.href='<?php echo montalink('script-delete-cliente'); ?>&amp;pagina=form-cad-cliente&amp;cdcliente=<?php echo $cdcliente; ?>'; else return false;" name="excluir" class="botao" />
	</p>
	-->	
	<p>
		<?php 
		if ($cdcliente) {
			echo '<input type="hidden" name="cdcliente" id="cdcliente" value="' , $cdcliente , '" />';
			
			echo label('Visualizar pedidos', '', false, 'width:160px;');
			echo '<a target="_blank" href="' , montalink('lista-pedido') , '&amp;pesquisar&amp;termo=' , $res['email'] , '">';
			echo '<img src="' , IMG , '/icons/lupa.png" alt="Visualizar pedidos" title="Visualizar pedidos" />';
			echo '</a>';
			echo '<br class="clear" />';
		}

		echo label('Nome do cliente', 'nmcliente', true, 'width:160px;'); ?>
		<input type="text" name="nmcliente" value="<?php echo $nmcliente;?>" id="nmcliente" style="width:300px;" maxlength="100" />
		<br />
		
		<?php echo label('Sexo', 'sexo', true, 'width:160px;'); ?>
		<select name="sexo" id="sexo" style="width:100px;">
			<?php $selected = $sexo == 'M' ? 'selected="selected"' : '' ; ?>
			<option value="M" <?php echo $selected; ?>>Masculino</option>
			<?php $selected = $sexo == 'F' ? 'selected="selected"' : '' ; ?>
			<option value="F" <?php echo $selected; ?>>Feminino</option>
		</select>
		<br />
		
		<?php echo label('Estado civil', 'cdestadocivil', false, 'width:160px;'); ?>
		<select name="cdestadocivil" id="cdestadocivil" style="width:100px;">
			<option value="null">--</option>
			<?php
			$sql = "select * from tb_estadocivil order by nmestadocivil";
			$sql = mysql_query($sql, $con);
			while ($res = mysql_fetch_assoc($sql)) {
				array_walk($res, 'array_var_show');
				$selected = $res['cdestadocivil'] == $cdestadocivil ? ' selected="selected" ' : '' ;
				echo '<option ' , $selected , ' value="' , $res['cdestadocivil'] , '">' , $res['nmestadocivil'] , '</option>';
			}
			?>
		</select>
		<br />
		
		<?php echo label('Data de nascimento', 'dtnascimento', false, 'width:160px;'); ?>
		<input type="text" name="dtnascimento" value="<?php echo $dtnascimento;?>" id="dtnascimento" class="mask-date" style="width:80px;" />
		<br />
		
		<?php echo label('Cidade/Estado', 'cdcidade', true, 'width:160px;'); ?>
		<input type="text" name="cdcidade" style="width:300px;" readonly="readonly" value="<?php echo $nmcidade . '/' . $sgestado; ?>" />
		<br />
		
		<?php echo label('Bairro', 'nmbairro', false, 'width:160px;'); ?>
		<input type="text" value="<?php echo $nmbairro;?>" name="nmbairro" id="nmbairro" style="width:300px;" maxlength="100" />
		<br />
		
		<?php echo label('Endereço', 'endereco', false, 'width:160px;'); ?>
		<input type="text" name="endereco" value="<?php echo $endereco; ?>" id="endereco" style="width:300px;" maxlength="100" />
		<br />
		
		<?php echo label('Nº', 'num', false, 'width:160px;'); ?>
		<input type="text" name="num" value="<?php echo $num;?>" id="num" style="width:50px;" maxlength="14" />
		
		<?php echo label('CEP', 'cep', false, 'float:none;'); ?>
		<input type="text" name="cep" id="cep" value="<?php echo $cep;?>" style="width:80px;" class="mask-cep" maxlength="14" />
		<br />
		
		<?php echo label('Complemento', 'complemento', false, 'width:160px;'); ?>
		<input type="text" value="<?php echo $complemento; ?>" name="complemento" id="complemento" style="width:300px;" maxlength="100" />
		<br />
		
		<?php echo label('E-mail', 'email', true, 'width:160px;'); ?>
		<input type="text" value="<?php echo $email; ?>" name="email" id="email" style="width:300px;" maxlength="100" />
		<br />
		
		<?php echo label('Telefone cel.', 'telcel', false, 'width:160px;');?>
		<input type="text" maxlength="14" value="<?php echo $telcel;?>" class="mask-fone" name="telcel" id="telcel" style="width:100px;" />
		<br />

		<?php echo label('Telefone resid.', 'telresid', false, 'width:160px;');?>
		<input type="text" maxlength="14" value="<?php echo $telresid;?>" class="mask-fone" name="telresid" id="telresid" style="width:100px;" />
		<br />
		
		<?php echo label('Receber news', 'recebernews', false, 'width:160px;'); ?>
		<?php $checked = $recebernews == 'S' ? ' checked="checked" ' : '' ; ?>
		<input type="checkbox" name="recebernews" value="S" id="recebernews" <?php echo $checked; ?> />
		&nbsp;&nbsp;&nbsp;
		<?php echo label('Receber SMS', 'receber_sms', false, 'float:none;'); ?>
		<?php $checked = $receber_sms == 'S' ? ' checked="checked" ' : '' ; ?>
		<input type="checkbox" name="receber_sms" value="S" id="receber_sms" <?php echo $checked; ?> />
		<br />
	</p>
	<div class="clear"></div>
	<p>
		<?php echo label('Ativo', 'ativo', false, 'width:160px;'); ?>
		<select name="ativo" id="ativo" style="width:100px;">
			<option value="S">Sim</option>
			<?php $selected = $ativo == 'N' ? 'selected="selected"' : '' ; ?>
			<option value="N" <?php echo $selected;?>>Não</option>
		</select>
		<br />
		
		<?php 
		echo label('Senha', 'senha', true, 'width:160px;');
		$senha = !$senha ? $senhamd5 : $senha ;
		?>
		<input type="password" name="senha" id="senha" maxlength="32" style="width:100px;" />
		<input type="hidden" name="senhamd5" id="senhamd5" value="<?php echo $senha;?>" maxlength="32" style="width:100px;" />
		<br />
		
		<?php echo label('Observação', 'observacao', false, 'width:160px;');?>
	</p>
	<div class="clear"></div>
	<p>
		<textarea rows="10" cols="100" name="observacao" id="observacao" style="width:750px; height:300px;"><?php echo $observacao;?></textarea>
	</p>

</form>

<?php
require(INCLUDES . '/inc.footer.php');