<?php
/**
 * Listagem de doações cadastradas
 */
include_once(CLASSES . '/class.Paginacao.php');

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

//destroi a sessao de controle de pag
if (isset($_SESSION['pagdoacao'])) {
	unset($_SESSION['pagdoacao'], $_SESSION['cddoador']);
}
?>

<div class="nav">
	> <a href="<?php echo montalink('cadastros'); ?>">Cadastros</a>
	> Doações
</div>

<?php require('inc.script-trata-pesquisa-doacao.php'); ?>

<form method="post" class="form" style="width:auto;" action="<?php echo montalink('lista-doacao'); ?>">
	<p>
		<?php echo label('COD.', 'cddoacao', false, 'width:90px;'); ?>
		<input type="text" name="cddoacao" id="cddoacao" style="width:100px;" value="<?php echo $_SESSION['search']['lista-doacao']['cddoacao']; ?>" maxlength="11" />
		
		<?php echo label('DOADOR', 'doador', false, 'float:none;'); ?>
		<input type="text" name="doador" id="doador" style="width:300px;" maxlength="100" value="<?php echo $_SESSION['search']['lista-doacao']['doador']; ?>" />
		<span class="dscampo">(NOME FANTASIA, RESPONSÁVEL) </span>
		<br />
		
		<?php echo label('TIPO', 'sgtpdoador', false, 'width:90px;'); ?>
		<select name="sgtpdoador" id="sgtpdoador" style="width:100px;">
			<option value="0">TODOS</option>
			<?php
			$sql = "select *
					from tb_tpdoador
					order by sgtpdoador";
			$sql = mysql_query($sql, $con);
			while ($res = mysql_fetch_assoc($sql)) {
				$selected = $_SESSION['search']['lista-doacao']['sgtpdoador'] == $res['sgtpdoador'] ? 'selected="selected"' : '' ;
				echo '<option ' , $selected , ' value="' , $res['sgtpdoador'] , '">' , $res['sgtpdoador'] , ' (' , $res['dstpdoador'] , ')</option>';
			}
			?>
		</select>
		
		<?php echo label('CONTATO', 'dtcontatoini', false, 'width:90px; float:none;'); ?>
		<input type="text" name="dtcontatoini" value="<?php echo $_SESSION['search']['lista-doacao']['dtcontatoini']; ?>" id="dtcontatoini" style="width:100px;" maxlength="20" class="datepicker" />
		&nbsp;a&nbsp;
		<input type="text" name="dtcontatofim" value="<?php echo $_SESSION['search']['lista-doacao']['dtcontatofim']; ?>"  id="dtcontatofim" style="width:100px;" maxlength="20" class="datepicker" />
		
		<?php echo label('RECEBIMENTO', 'dtrecini', false, 'float:none; margin-left:20px;'); ?>
		<input type="text" value="<?php echo $_SESSION['search']['lista-doacao']['dtrecini']; ?>" name="dtrecini" id="dtrecini" style="width:100px;" maxlength="20" class="datepicker" />
		&nbsp;a&nbsp;
		<input type="text" value="<?php echo $_SESSION['search']['lista-doacao']['dtrecfim']; ?>" name="dtrecfim" id="dtrecfim" style="width:100px;" maxlength="20" class="datepicker" />
		<br />
		
		<?php echo label('OPERADOR', 'cdusuario', false, 'width:90px;'); ?>
		<select name="cdusuario" id="cdusuario" style="width:259px;">
			<option value="0">TODOS</option>
			<?php
			$sql = "select distinct sis_usuario.cdusuario, sis_usuario.nmusuario
						from sis_usuario
							 inner join sis_usuario_tpusuario stp on sis_usuario.cdusuario = stp.cdusuario
							 inner join sis_tpusuario on stp.cdtpusuario = sis_tpusuario.cdtpusuario
						where 
							sis_tpusuario.sgtpusuario = 'OP'
						order by
							sis_usuario.cdusuario,
							sis_usuario.nmusuario";
			$sql = mysql_query($sql, $con);
			while ($res = mysql_fetch_assoc($sql)) {
				$selected = $res['cdusuario'] == $_SESSION['search']['lista-doacao']['cdusuario'] ? ' selected="selected" ' : '' ;
				echo '<option ' , $selected , ' value="' , $res['cdusuario'] , '">' , $res['cdusuario'] , ' - ' , $res['nmusuario'] , '</option>';
			}
				?>
		</select>
		
		<?php echo label('CANCELADO', 'cancelado', false, 'float:none; margin-left:20px;'); ?>
		<select name="cancelado" id="cancelado" style="width:157px;">
			<option value="0">TODOS</option>
			<?php $selected = $_SESSION['search']['lista-doacao']['cancelado'] == 'S' ? 'selected="selected"' : '' ; ?>
			<option value="S" <?php echo $selected; ?>>SIM</option>
			<?php $selected = $_SESSION['search']['lista-doacao']['cancelado'] == 'N' ? 'selected="selected"' : '' ; ?>
			<option value="N" <?php echo $selected; ?>>NÃO</option>
		</select>
	</p>
	<p style="text-align:center; margin-top:10px;">
		<input type="submit" class="botao" value="Pesquisar" name="pesquisar" id="pesquisar" />
		<input type="submit" class="botao" value="Limpar" name="limpar" id="limpar" />
	</p>
</form>
<br />
<br />

<?php
//exit();
//msg
//caso exista mensagem para exibir
if (isset($_SESSION['msg']['caddoacao'])) {
	echo show_session_msg('caddoacao');
	destroy_session_msg('caddoacao');
}

//operação
echo '<p class="operacao">';
echo '<img src="' , IMG , '/icons/add.png" alt="Cadastrar" title="Cadastrar" />';
echo '<a href="' , montalink('form-cad-doacao') , '">Cadastrar doação</a>';
echo '</p>';
echo '<div class="clear"></div>';

//seleciona o total de doacoes cadastradas
$sql = "select count(tb_doacao.cddoacao) as tot
		from 
			tb_doacao
		where tb_doacao.excluido = 'N' $where";
$sql = mysql_query($sql, $con);
$sql = mysql_fetch_assoc($sql);

if (!$sql['tot']) {
	echo msg('information', 'Nenhuma doação encontrada');
	require(INCLUDES . '/inc.footer.php');
	exit();
}

//Paginação
$_SEESION['pag-lista-doacao'] = (int)$_GET['pag'] ? (int)$_GET['pag'] : 1 ;
$paginacao = new Paginacao();
$paginacao->set_num_reg_pag(20);
$paginacao->set_num_reg_tot($sql['tot']);
$paginacao->set_site_link(montalink('lista-doacao') . $link);
$paginacao->set_pagina_atual($_SEESION['pag-lista-doacao']);

$paginacao->show();
echo '<strong>' , $paginacao->get_num_reg_tot() , '</strong> registro(s) encontrado(s)';
?>

<table style="width:100%;">
	<?php
	echo $table = '<tr>
		<th style="width:80px;">COD.</th>
		<th style="width:70px;">TIPO</th>
		<th style="width:90px;">RECEBIMENTO</th>
		<th>RESPONSÁVEL</th>
		<th>EMPRESA</th>
		<th style="width:90px;">DT. CONTATO</th>
		<th style="width:100px;">VALOR</th>
		<th style="width:80px;">CANCELADO</th>
		<th colspan="3">AÇÕES</th>';
/*
	$sql = "select 
			tb_doacao.*,
			tb_doador.nmresponsavel,
			tb_tpdoador.sgtpdoador,
			tb_doador.nmfantasia
		from 
			tb_doacao,
			tb_doador,
			tb_tpdoador
		where
			tb_doacao.cddoador = tb_doador.cddoador and
			tb_doador.cdtpdoador = tb_tpdoador.cdtpdoador and
			tb_doacao.excluido = 'N'
			$where
		order by
			tb_doacao.cancelado desc,
			tb_doacao.dtrec desc,
			tb_doador.nmresponsavel,
			tb_doacao.dtcontato desc
		limit
			" . $paginacao->get_reg_ini() . ', ' . $paginacao->get_num_reg_pag();
*/

$sql = "select 
			tb_doacao.*
		from 
			tb_doacao
		where
			tb_doacao.excluido = 'N'
			$where
		order by
			tb_doacao.cancelado desc,
			tb_doacao.dtrec desc,
			tb_doacao.dtcontato desc
		limit
			" . $paginacao->get_reg_ini() . ', ' . $paginacao->get_num_reg_pag();
	$sql = mysql_query($sql, $con);
	
	$x = 0;
	while ($res = mysql_fetch_assoc($sql)) {
		//mostra_array($res);
		$lista = $x++ % 2 != 0 ? 'lista_par' : 'lista_impar' ;
		?>
		<tr class="<?php echo $lista; ?>">
			<td style="text-align:center;"><?php echo $res['cddoacao']; ?></td>
			<td style="text-align:center;"><?php echo $res['sgtpdoador']; ?></td>
			<td style="text-align:center;"><?php echo inverte_formato_data($res['dtrec'], '/'); ?></td>
			<td><?php echo $res['nmresponsavel']; ?></td>
			<td><?php echo $res['nmfantasia']; ?></td>
			<td style="text-align:center;"><?php echo $res['dtcontato'] != '' ? inverte_formato_data($res['dtcontato'], '/') : '' ; ?></td>
			<td style="text-align:center;">R$<?php echo number_show($res['vldoacao']); ?></td>
			<td style="text-align:center;"><?php echo sim_nao($res['cancelado']); ?></td>
			<td style="text-align:center; width:25px;">
				<a target="_blank" href="<?php echo montalink('rel-recibo'); ?>&amp;cddoacao=<?php echo $res['cddoacao']; ?>">
					<img src="<?php echo IMG; ?>/icons/print.png" alt="Imprimir" title="Imprimir" />
				</a>
			</td>
			
			<td style="text-align:center; width:25px;">
				<a href="'<?php echo montalink('form-cad-doacao'); ?>&amp;cddoacao=<?php echo $res['cddoacao']; ?>">
					<img src="<?php echo IMG; ?>/icons/editar.png" alt="editar" title="Editar" />
				</a>
			</td>
			
			<td style="text-align:center; width:25px;">
				<a onclick="return confirma(0);" href="<?php echo montalink('script-delete-doacao'); ?>&amp;cddoacao=<?php echo $res['cddoacao']; ?>">
					<img src="<?php echo IMG; ?>/icons/delete.png" alt="excluir" title="Excluir" />
				</a>
			</td>
		</tr>
		<?php
	}

	echo $table;
	?>
	</tr>
</table>

<?php
echo $paginacao->show();

require(INCLUDES . '/inc.footer.php');