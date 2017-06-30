<?php
/**
 * Formulário de filtro de doadores por status
 */

$array_include_arq = array(
array('type' => 'text/css', 'name' => 'jquery/ui.all.css'),
array('type' => 'text/javascript', 'name' => 'ui.core.js'),
array('type' => 'text/javascript', 'name' => 'ui.datepicker.js'),
array('type' => 'text/javascript', 'name' => 'my.datepicker.js'),
array('type' => 'text/javascript', 'name' => 'jquery.maskedinput-1.2.1.js'),
array('type' => 'text/javascript', 'name' => 'masks.js'),
array('type' => 'text/javascript', 'name' => 'funcoes.js'),
array('type' => 'text/javascript', 'name' => 'jquery.maskMoney.0.2.js'),
array('type' => 'text/javascript', 'name' => 'money-masks.js')
);

require(INCLUDES . '/inc.header.php');
?>

<div class="nav">
	> <a href="<?php echo montalink('relatorios') ?>">Relatórios</a>
	> Aniversariantes por período
</div>

<form method="post" style="width:400px;" target="_blank" action="<?php echo montalink('rel-pdf-aniversariante'); ?>" class="form">
	<p>
		<?php echo label('PERÍODO', 'dtini', true); ?>
		<input type="text" name="dtini" id="dtini" style="width:90px;" class="datepicker" />
		&nbsp;a&nbsp;
		<input type="text" name="dtfim" id="dtfim" style="width:90px;" class="datepicker" />
		
		<?php echo label('STATUS', 'ativo', false); ?>
		<select name="ativo" style="width:150px;">
			<option value="0">TODOS</option>
			<option value="S">SIM</option>
			<option value="N">NÃO</option>
		</select>		
	</p>
	<p style="text-align:center; margin-top:20px;">
		<input type="submit" name="enviar" value="Enviar" class="botao" />
	</p>
</form>

<?php
require(INCLUDES . '/inc.footer.php');