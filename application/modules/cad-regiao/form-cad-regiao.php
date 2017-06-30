<?php
/**
 * Formulário de cadastro de região
 */
require(INCLUDES . '/inc.header.php');
?>

<div class="nav">
	> <a href="<?php echo montalink('cadastros'); ?>">Cadastros</a>
	> <a href="<?php echo montalink('lista-regiao'); ?>">Regiões de recebimento</a>
	> Cadastrar região
</div>

<form method="post" class="form" action="<?php echo montalink($pagina); ?>">
	<p class="navbar">
		<input type="button" onclick="window.location.href='<?php echo montalink('form-cad-regiao');?>';" value="NOVO" name="novo" class="botao" />
		<input type="submit" value="SALVAR" name="salvar" class="botao" />
		<input type="button" value="CANCELAR" onclick="window.location.href='<?php echo montalink('lista-regiao'); ?>'" name="cancelar" class="botao" />
		<?php $disabled = !$cdempresa ? ' disabled="disabled" ' : '' ; ?>
		<input type="button" <?php echo $disabled; ?> value="EXCLUIR" onclick="if(confirma(0)) window.location.href='<?php echo montalink('script-delete-regiao'); ?>&amp;pag=form-cad-regiao&amp;cdregiao=<?php echo $cdregiao; ?>'; else return false;" name="excluir" class="botao" />
	</p>
	<p>
		<?php echo label('DESCRIÇÃO', 'dsregiao', true); ?>
		<input type="text" name="dsregiao" id="dsregiao" style="width:300px;" value="<?php echo $dsregiao; ?>" />
		<br />
		
		<?php echo label('BAIRROS', 'cdbairro', true); ?>
		<div style="overflow:scroll; border:#CCC 1px solid; width:400px; height:200px;">
			<?php 
			for ($x = 0; $x <= 50; $x++) {
				$style = $x < 5 ? 'style="background-color: lightgreen; border:#fff 1px solid;"' : '' ;
				$checked = $x < 5 ? ' checked="checked" ' : '' ;
				echo '<div ' , $style , '>';
				echo '<input type="checkbox" ' , $checked , ' name="cdbairro[]" value="0" /> IPATINGA - NOME DO BAIRRO <br />';
				echo '</div>';
			}
			?>
		</div>
	</p>
	<p>
		<?php echo label('OBSERVAÇÕES', 'observacoes', false); ?>
		<textarea name="observacoes" id="observacoes" cols="100" rows="10" style="width:400px; height:50px;"><?php echo $observacoes; ?></textarea>
	</p>
	<p class="navbar" style="margin:10px 0 0 0;">
		<input type="button" onclick="window.location.href='<?php echo montalink('form-cad-regiao');?>';" value="NOVO" name="novo" class="botao" />
		<input type="submit" value="SALVAR" name="salvar" class="botao" />
		<input type="button" value="CANCELAR" onclick="window.location.href='<?php echo montalink('lista-regiao'); ?>'" name="cancelar" class="botao" />
		<?php $disabled = !$cdempresa ? ' disabled="disabled" ' : '' ; ?>
		<input type="button" <?php echo $disabled; ?> value="EXCLUIR" onclick="if(confirma(0)) window.location.href='<?php echo montalink('script-delete-regiao'); ?>&amp;pag=form-cad-regiao&amp;cdregiao=<?php echo $cdregiao; ?>'; else return false;" name="excluir" class="botao" />
	</p>
</form>

<?php
require(INCLUDES . '/inc.footer.php');