<?php
/**
 * Formulário de start da rotina de cadastro de doações
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
array('type' => 'text/css', 'name' => 'colorbox.css'),
array('type' => 'text/javascript', 'name' => 'jquery.colorbox.js'),
array('type' => 'text/javascript', 'name' => 'colorbox.js')
);

require(INCLUDES . '/inc.header.php');
?>

<div class="nav">
	> <a href="<?php echo montalink('cadastros') ?>">Cadastros</a>
	> Rotina de cadastro de doações
</div>

<form method="post" style="width:350px;" class="form" action="<?php echo montalink('script-rot-cad-doacao'); ?>">
	<p>
		<?php echo label('MÊS/ANO', 'mes', true); ?>
		<select name="mes" style="width:50px;">
			<option value="0">-</option>
			<?php
			for ($x = 1; $x <= 12; $x++) {
				echo '<option value="' , $x , '">' , $x , '</option>';
			}
			?>
		</select>
		/
		<select name="ano" style="width:90px;">
			<option value="0">-</option>
			<?php
			for ($x = 2014; $x <= date('Y') + 2; $x++) {
				echo '<option value="' , $x , '">' , $x , '</option>';
			}
			?>
		</select>
	</p>
	<br />
	<p style="text-align:center;">
		<input type="submit" onclick="return confirma('Confirma a execução da rotina?\nA ação não poderá ser revertida.');" name="enviar" value="EXECUTAR" class="botao" />
	</p>
	
	<br />
	<br />
	
	<p class="dscampo" style="font-size:12px;">
		<strong>OBSERVAÇÕES</strong>
		<br />
		1. Ao executar a rotina para o mês e ano selecionados, o sistema realizará 
		automaticamente o lançamento das doações para os doadores ativos utilizando
		como base a sua periodicidade e dia previsto para doação.
		<br /><br />
		2. Somente será realizado o lançamento para o doador caso não seja encontrada
		nenhuma doação para o mesmo no mês e ano selecionados.
	</p>
</form>

<table style="width:100%;" class="table">
	<?php
	$table = '<tr>
		<th style="width:25%;">DATA</th>
		<th style="width:25%;">MÊS/ANO</th>
		<th style="width:25%;">TOTAL DE RECIBOS GERADOS</th>
		<th style="width:25%;">USUÁRIO</th>
	</tr>';
	echo $table;
	
	$sql = "select *
			from tb_logdoacao
			order by dtlog desc";
	$sql = mysql_query($sql, $con);
	
	$x = 0;
	while ($res = mysql_fetch_assoc($sql)) {
		$list = ++$x % 2 == 0 ? 'lista_par' : 'lista_impar' ;
		echo '<tr class="',$list,'">';
		echo '<td style="text-align:center;">' , date('d/m/Y H:i:s', strtotime($res['dtlog'])) , '</td>';
		echo '<td style="text-align:center;">' , $res['mes'] , '/' , $res['ano'] , '</td>';
		echo '<td style="text-align:center;">' , $res['totdoacao'] , '</td>';
		echo '<td style="text-align:center;">' , $res['nmusuario'] , '</td>';
		echo '</tr>';
	}
	
	echo $table; 
	?>
</table>

<?php
require(INCLUDES . '/inc.footer.php');
