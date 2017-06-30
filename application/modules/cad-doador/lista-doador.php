<?php
/**
 * Listagem das empresas cadastradas
 * - Criado em 06/01/2010
 * 
 * @author Niury Martins - http://niurymartins.com.br
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
require('script-trata-pesquisa-doador.php');
?>
<div class="nav">
	> <a href="<?php echo montalink('cadastros');?>">Cadastros</a>
	> Doadores
</div>

<fieldset style="width:auto; margin-bottom:30px;">
	<legend>PESQUISA</legend>
	<form method="post" action="<?php echo montalink('lista-doador');?>">
		<p>
			<?php 
			echo label('Tipo', 'cdtpdoador', false);
			$sql = "select *
					from tb_tpdoador
					order by sgtpdoador";
			$sql = mysql_query($sql, $con);
			?>
			<select name="cdtpdoador" id="cdtpdoador" style="width:100px;">
				<option value="0">TODOS</option>
				<?php
				while ($res = mysql_fetch_assoc($sql)) {
					$selected = $res['cdtpdoador'] == $_SESSION['search']['lista-doador']['cdtpdoador'] ? 'selected="selected"' : '' ;
					echo '<option ' , $selected , ' value="' , $res['cdtpdoador'] , '">' , $res['sgtpdoador'] , ' (' , $res['dstpdoador'] , ')</option>';
				}
				?>
			</select>
			
			<?php echo label('operador', 'cdusuario', false, 'float:none;'); ?>
			<select name="cdusuario" id="cdusuario" style="width:300px;">
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
					$selected = $res['cdusuario'] == $_SESSION['search']['lista-doador']['cdusuario'] ? ' selected="selected" ' : '' ;
					echo '<option ' , $selected , ' value="' , $res['cdusuario'] , '">' , $res['cdusuario'] , ' - ' , $res['nmusuario'] , '</option>';
				}
				?>
			</select>
			<?php echo label('Ativo', 'ativo', false, 'float:none;'); ?>
			<select name="ativo" id="ativo" style="width:100px;">
				<option value="0">TODOS</option>
				<?php $selected = ($_SESSION['search']['lista-doador']['ativo'] == 'S') ? 'selected="selected"' : '' ; ?>
				<option value="S" <?php echo $selected; ?>>Sim</option>
				<?php $selected = ($_SESSION['search']['lista-doador']['ativo'] == 'N') ? 'selected="selected"' : '' ; ?>
				<option value="N" <?php echo $selected; ?>>Não</option>
			</select>
			<br />
			
			<?php echo label('Termo', 'termo', false); ?>
			<input type="text" value="<?php echo $_SESSION['search']['lista-doador']['termo'];?>" style="width:350px;" name="termo" id="termo" maxlength="100" />
			
			<?php echo label('Ultimo contato', 'dtcontaini', false, 'float:none;'); ?>
			<input type="text" name="dtcontatoini" value="<?php echo $_SESSION['search']['lista-doador']['dtcontatoini']; ?>" class="datepicker" id="dtcontatoini" style="width:100px;" />
			&nbsp;a&nbsp;
			<input type="text" name="dtcontatofim" value="<?php echo $_SESSION['search']['lista-doador']['dtcontatofim']; ?>" class="datepicker" id="dtcontatofim" style="width:100px;" />
			<br />
			<label>&nbsp;</label><span class="dscampo">(COD.,  CNPJ, CPF, NOME FANTASIA, RAZÃO SOCIAL, RESPONSÁVEL, TELEFONES, ENDEREÇO, E-MAIL)</span>
			
		</p>
		<p style="text-align:center; margin-top:20px;">
			<input type="submit" name="pesquisar" id="pesquisar" value="Pesquisar" class="botao" />
			<input type="submit" name="limpar" id="Limpar" value="Limpar" class="botao" />
		</p>
	</form>
</fieldset>

<?php 
//operação
echo '<p class="operacao">';
echo '<img src="' , IMG , '/icons/add.png" alt="Cadastrar" title="Cadastrar" />';
echo '<a href="' , montalink('form-cad-doador') , '">Cadastrar doador</a>';
echo '</p>';
echo '<div class="clear"></div>';

//caso exista mensagem para exibir
if (isset($_SESSION['msg']['caddoador'])) {
	echo show_session_msg('caddoador');
	destroy_session_msg('caddoador');
}

//seleciona o total de empresas cadastradas
$sql = "select count(distinct tb_doador.cddoador) as num
		from 
			tb_doador
			inner join tb_tpdoador on tb_doador.cdtpdoador = tb_tpdoador.cdtpdoador
			inner join tb_cidade on tb_doador.cdcidade = tb_cidade.cdcidade 
			left join tb_doacao on tb_doador.cddoador = tb_doacao.cddoador
		$where";
$sql = mysql_query($sql, $con);
$sql = mysql_fetch_assoc($sql);

if (!$sql['num']) {
	echo msg('information', 'Nenhum doador cadastrado');
	require(INCLUDES . '/inc.footer.php');
	exit();
}

//Paginação
$_SEESION['pag-lista-doador'] = (int)$_GET['pag'] ? (int)$_GET['pag'] : 1 ;
$paginacao = new Paginacao();
$paginacao->set_num_reg_pag(20);
$paginacao->set_num_reg_tot($sql['num']);
$paginacao->set_site_link(montalink('lista-doador') . $link);
$paginacao->set_pagina_atual($_SEESION['pag-lista-doador']);

//listagem dos doadores
/*
$sql = "select
			tb_doador.cddoador,
			tb_doador.ativo,
			tb_tppessoa.nmtppessoa,
			tb_doador.nmfantasia,
			tb_doador.nmresponsavel,
			tb_cidade.nmcidade,
			tb_estado.sgestado,
			tb_doador.nmbairro,
			tb_doador.telefone1,
			tb_doador.telefone2,
			tb_doador.telefone3,
			sis_usuario.cdusuario,
			sis_usuario.nmusuario,
			tb_tpdoador.sgtpdoador,
			sum(tb_doacao.vldoacao) as totdoacao,
			date_format(max(tb_doacao.dtcontato), '%d/%m/%Y') as dtcontato
		from 
			tb_doador 
			inner join tb_cidade on tb_doador.cdcidade = tb_cidade.cdcidade 
			inner join tb_estado on tb_cidade.cdestado = tb_estado.cdestado
			inner join tb_tpdoador on tb_doador.cdtpdoador = tb_tpdoador.cdtpdoador
			inner join tb_tppessoa on tb_doador.cdtppessoa = tb_tppessoa.cdtppessoa
			left join tb_doacao on (tb_doador.cddoador = tb_doacao.cddoador and tb_doacao.excluido = 'N')
			left join sis_usuario on tb_doador.cdusuario = sis_usuario.cdusuario
		where 
			1=1 $where
		group by
			tb_doador.cddoador,
			tb_tppessoa.nmtppessoa,
			tb_doador.nmfantasia,
			tb_doador.nmresponsavel,
			tb_cidade.nmcidade,
			tb_estado.sgestado,
			tb_doador.nmbairro,
			tb_doador.telefone1,
			tb_doador.telefone2,
			tb_doador.telefone3,
			sis_usuario.cdusuario,
			sis_usuario.nmusuario,
			tb_tpdoador.sgtpdoador
		order by 
			tb_doador.nmresponsavel
		limit
			" . $paginacao->get_reg_ini() . ', ' . $paginacao->get_num_reg_pag();
*/

?>
<strong><?php echo $sql['num']; ?></strong> registro(s) encontrado(s)
<?php

$sql = "select
			tb_doador.cddoador,
			tb_doador.ativo,
			tb_doador.endereco,
			tb_tppessoa.nmtppessoa,
			tb_doador.nmfantasia,
			tb_doador.nmresponsavel,
			tb_doador.vltotdoacao,
			date_format(tb_doador.ultdtcontato, '%d/%m/%Y') as ultdtcontato,
			tb_cidade.nmcidade,
			tb_estado.sgestado,
			tb_doador.nmbairro,
			tb_doador.telefone1,
			tb_doador.telefone2,
			tb_doador.telefone3,
			sis_usuario.cdusuario,
			sis_usuario.nmusuario,
			tb_tpdoador.sgtpdoador
		from 
			tb_doador 
			inner join tb_cidade on tb_doador.cdcidade = tb_cidade.cdcidade 
			inner join tb_estado on tb_cidade.cdestado = tb_estado.cdestado
			inner join tb_tpdoador on tb_doador.cdtpdoador = tb_tpdoador.cdtpdoador
			inner join tb_tppessoa on tb_doador.cdtppessoa = tb_tppessoa.cdtppessoa
			inner join sis_usuario on tb_doador.cdusuario = sis_usuario.cdusuario
		$where
		order by 
			tb_doador.nmresponsavel
		limit
			" . $paginacao->get_reg_ini() . ', ' . $paginacao->get_num_reg_pag();

//mostra_array($sql);

$sql = mysql_query($sql, $con);

$paginacao->show();

echo '<table style="width:100%;">';
echo $table = '<tr>
			<th style="width:5%;">TIPO</th>
			<th style="width:20%;">RESPONSÁVEL</th>
			<th style="width:20%;">NOME FANTASIA</th>
			<th style="width:10%;">CIDADE/UF</th>
			<th style="width:10%;">BAIRRO</th>
			<th style="width:20%;">ENDEREÇO</th>
			<th style="width:10%;">TELEFONE(S)</th>
			<th style="width:10%;">OPERADOR</th>
			<th style="width:10%;" title="Data do ultimo contato">ULT. CONTATO</th>
			<th style="width:10%;">TOT. DOAÇÕES</th>
			<th>ATIVO</th>
			<th colspan="2">AÇÕES</th>
			</tr>';

$x = 0;
while ($res = mysql_fetch_assoc($sql)) {
	array_walk($res, 'array_var_show');
	?>
	<tr class="<?php echo $lista = $x++ % 2 != 0 ? 'lista_par' : 'lista_impar' ; ?>">
		<td style="text-align:center;"><?php echo $res['sgtpdoador']; ?></td>
		<td><?php echo $res['nmresponsavel']; ?></td>
		<td><?php echo $res['nmfantasia']; ?></td>
		<td><?php echo $res['nmcidade'] , '/' , $res['sgestado']; ?></td>
		<td><?php echo $res['nmbairro']; ?></td>
		<td><?php echo $res['endereco']; ?></td>
		<td style="text-align:center;">
			<?php
			echo $res['telefone1'];
			if ($res['telefone2']) {
				echo ' ' , $res['telefone2'];
			}

			if ($res['telefone3']) {
				echo ' ' , $res['telefone3'];
			}
			?>
		</td>
		<td><?php echo $res['nmusuario'] ?></td>
		<td style="text-align:center;"><?php echo $res['ultdtcontato'] ?></td>
		<td style="text-align:center;">R$<?php echo number_show($res['vltotdoacao']); ?></td>
		<td style="text-align:center;"><?php echo sim_nao($res['ativo']); ?></td>
		
		<td style="text-align:center;">
		<a href="<?php echo montalink('form-cad-doador') , '&amp;cddoador=' , $res['cddoador']; ?>">
		<img src="<?php echo IMG; ?>/icons/editar.png" alt="editar" title="Editar" />
		</a>
		</td>
	
		<td style="text-align:center;">
		<a onclick="return confirma(0);" href="<?php echo montalink('script-delete-doador') , '&amp;cddoador=' , $res['cddoador']; ?>">
		<img src="<?php echo IMG; ?>/icons/delete.png" alt="excluir" title="Excluir" />
		</a>
		</td>
	</tr>
<?php
}

echo $table;
echo '</table>';

$paginacao->show();


?>

<div class="clear"></div>

<fieldset style="width:auto; margin-bottom:30px; background:none;">
	<legend>TOTALIZAÇÃO DE DOADORES</legend>
	<?php
	$sql = "select
			      count(tb_doador.cddoador) as tot
			from 
			     tb_doador
			     inner join tb_tpdoador on tb_doador.cdtpdoador = tb_tpdoador.cdtpdoador
			where 
				tb_doador.ativo = 'S'";
	$sql = mysql_query($sql, $con);
	$sql = mysql_fetch_assoc($sql);
	echo '<strong>ATIVOS:</strong> ' , $sql['tot'] , '<br />';

	$sql = "select
			      tb_tpdoador.sgtpdoador,
			      count(tb_doador.cddoador) as tot
			from 
			     tb_doador
			     inner join tb_tpdoador on tb_doador.cdtpdoador = tb_tpdoador.cdtpdoador 
			where
			     tb_doador.ativo = 'S'
			group by 
			      tb_tpdoador.sgtpdoador
			order by
			      tb_tpdoador.sgtpdoador";
	$sql = mysql_query($sql, $con);
	$x = 0;
	while ($res = mysql_fetch_assoc($sql)) {

		if ($x > 0) {
			echo ' | ';
		}

		echo '<strong>' , $res['sgtpdoador'] , ' = </strong> ' , $res['tot'];
		$x++;
	}
	?>
	
	<br /><br />
	<?php
	$sql = "select
			      count(tb_doador.cddoador) as tot
			from 
			     tb_doador
			     inner join tb_tpdoador on tb_doador.cdtpdoador = tb_tpdoador.cdtpdoador
			where 
				tb_doador.ativo = 'N'";
	$sql = mysql_query($sql, $con);
	$sql = mysql_fetch_assoc($sql);
	echo '<strong>INATIVOS:</strong> ' , $sql['tot'] , '<br />';

	$sql = "select
			      tb_tpdoador.sgtpdoador,
			      count(tb_doador.cddoador) as tot
			from 
			     tb_doador
			     inner join tb_tpdoador on tb_doador.cdtpdoador = tb_tpdoador.cdtpdoador 
			where
			     tb_doador.ativo = 'N'
			group by 
			      tb_tpdoador.sgtpdoador
			order by
			      tb_tpdoador.sgtpdoador";
	$sql = mysql_query($sql, $con);
	$x = 0;
	while ($res = mysql_fetch_assoc($sql)) {

		if ($x > 0) {
			echo ' | ';
		}

		echo '<strong>' , $res['sgtpdoador'] , ' = </strong> ' , $res['tot'];
		$x++;
	}
	?>
</fieldset>

<?php
require(INCLUDES . '/inc.footer.php');