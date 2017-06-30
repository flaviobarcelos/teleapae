<?php
if ($cddoador) {
	//operação
	echo '<p class="operacao" style="margin:10px 10px 15px 0;">';
	echo '<img src="' , IMG , '/icons/add.png" alt="Cadastrar" title="Cadastrar" />';
	echo '<a href="' , montalink('form-cad-doacao') , '&amp;pag=form-cad-doador&amp;cddoador=' , $cddoador , '">Cadastrar doação</a>';
	echo '</p>';
	echo '<div class="clear"></div>';
	
	if (isset($_SESSION['msg']['caddoacao'])) {
		echo show_session_msg('caddoacao');
		destroy_session_msg('caddoacao');
	}

	$sql = "select *
			from tb_doacao
			where cddoador = '$cddoador' and
				  excluido = 'N'
			order by dtrec desc";
	$sql = mysql_query($sql, $con);

	if (!mysql_num_rows($sql)) {
		echo msg('information', 'Nenhuma doação cadastrada');
	}
	else {
	?>
	<div style=" overflow:scroll; height:580px;">
		<table class="table" style="width:100%;">
			<tr>
				<th>COD.</th>
				<th>CONTATO</th>
				<th>RECEBIMENTO</th>
				<th>VALOR</th>
				<th>CANCELADO</th>
				<th colspan="3" style="width:60px;">AÇÕES</th>
			</tr>
			<?php 
			$x = 0;
			while ($res = mysql_fetch_assoc($sql)) {
				$lista = $x++ % 2 == 0 ? 'lista_impar' : 'lista_par' ;
				$red = $res['cancelado'] == 'S' ? 'color:#FF0000;' : '' ;
				echo '<tr class="' , $lista , '" ' , $red , '>';
				echo '<td style="text-align:center;' , $red , '">' , $res['cddoacao'] , '</td>';
				$res['dtcontato'] = valida_data($res['dtcontato']) ? inverte_formato_data($res['dtcontato'], '/') : '' ;
				echo '<td style="text-align:center;',$red,'">' , $res['dtcontato'] , '</td>';
				$res['dtrec'] = valida_data($res['dtrec']) ? inverte_formato_data($res['dtrec'], '/') : '' ;
				echo '<td style="text-align:center;',$red,'">' , $res['dtrec'] , '</td>';
				echo '<td style="text-align:center;',$red,'">R$' , number_show($res['vldoacao']) , '</td>';
				echo '<td style="text-align:center;',$red,'">' , sim_nao($res['cancelado']) , '</td>';
				?>
				<td style="text-align:center; width:25px;">
					<a target="_blank" href="<?php echo montalink('rel-recibo') , '&amp;cddoacao=' , $res['cddoacao']; ?>">
						<img src="<?php echo IMG; ?>/icons/print.png" alt="Imprimir" title="Imprimir" />
					</a>
				</td>
				
				<td style="text-align:center; width:25px;">
					<a href="<?php echo montalink('form-cad-doacao') , '&amp;pag=form-cad-doador&amp;cddoacao=' , $res['cddoacao']; ?>&amp;cddoador=<?php echo $cddoador; ?>">
						<img src="<?php echo IMG; ?>/icons/editar.png" alt="editar" title="Editar" />
					</a>
				</td>
				
				<td style="text-align:center; width:25px;">
					<a onclick="return confirma(0);" href="<?php echo montalink('script-delete-doacao') , '&amp;pag=form-cad-doador&amp;cddoacao=' , $res['cddoacao']; ?>&amp;cddoador=<?php echo $cddoador; ?>">
						<img src="<?php echo IMG; ?>/icons/delete.png" alt="excluir" title="Excluir" />
					</a>
				</td>
				<?php
				echo '</tr>';
			}
			?>
		</table>
	</div>
	<br />
	
	<?php
	$sql = "select 
				sum(case when cancelado = 'N' then vldoacao else 0 end) as totre,
				sum(case when cancelado = 'S' then vldoacao else 0 end) as totca
			from tb_doacao
			where cddoador = '$cddoador' and
				  excluido = 'N'";
	$sql = mysql_query($sql, $con);
	$sql = mysql_fetch_assoc($sql);
	?>
	
	<strong>RECEBIDO:</strong> R$<?php echo number_show($sql['totre']); ?>
	<br />
	
	<strong>CANCELADO:</strong> R$<?php echo number_show($sql['totca']); ?>
</div>
<div class="clear"></div>

<?php
	}
}
?>