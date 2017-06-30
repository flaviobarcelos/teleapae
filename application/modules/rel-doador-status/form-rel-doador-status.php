<?php
/**
 * Formulário de filtro de doadores por status
 */
require(INCLUDES . '/inc.header.php');
?>

<div class="nav">
	> <a href="<?php echo montalink('relatorios') ?>">Relatórios</a>
	> Doadores por status
</div>

<form method="post" target="_blank" style="width:400px;" action="<?php echo montalink('rel-pdf-doador-status'); ?>" class="form">
	<p>
		<?php echo label('TIPO', 'cdtpdoador', false); ?>
		<select name="cdtpdoador" style="width:150px;">
			<option value="0">TODOS</option>
			<?php
			$sql = "select *
					from tb_tpdoador
					order by sgtpdoador";
			$sql = mysql_query($sql, $con);
			while ($res = mysql_fetch_assoc($sql)) {
				echo '<option value="' , $res['cdtpdoador'] , '">' , $res['sgtpdoador'] , ' (' , $res['dstpdoador'] , ')</option>';
			}
			?>
		</select>
		<br />
		
		<?php echo label('ATIVO', 'ativo', false); ?>
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