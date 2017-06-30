<?php
/**
 * lista os dados de uma oferta
 * - Criado em 02/03/2011
 * 
 * @author Niury Martins - http://niurymartins.com.br
 */
require(INCLUDES . '/inc.header-blank.php');
$cdoferta = (int)$_GET['cdoferta'];

if (!$cdoferta) {
	echo msg('error', MSG_ERROR);
	require(INCLUDES . '/inc.footer-blank.php');
}

/*
$sql = "select
			tb_oferta.cdoferta,
			tb_oferta.nmoferta,
			date_format(tb_oferta.dtinicio, '%d/%m/%Y %H:%i:%s') as dtinicio,
			date_format(tb_oferta.dttermino, '%d/%m/%Y %H:%i:%s') as dttermino,
			date_format(tb_oferta.dtvalidade, '%d/%m/%Y') as dtvalidade,
			date_format(tb_oferta.dtliberacaocupom, '%d/%m/%Y') as dtliberacaocupom,
			tb_oferta.vloriginal,
			tb_oferta.vlpromocional,
			#sitpedido
			sum(case when tb_sitpedido.sgsitpedido = 'AP' then tb_pedido.qtd else 0 end) 'aguardando_pagto',
			sum(case when tb_sitpedido.sgsitpedido = 'EA' then tb_pedido.qtd else 0 end) 'em_analise',
			sum(case when tb_sitpedido.sgsitpedido = 'A' then tb_pedido.qtd else 0 end) 'aprovado',
			sum(case when tb_sitpedido.sgsitpedido = 'C' then tb_pedido.qtd else 0 end) 'cancelado',
			sum(case when tb_sitpedido.sgsitpedido = 'E' then tb_pedido.qtd else 0 end) 'expirado',
			sum(case when tb_sitpedido.sgsitpedido = 'D' then tb_pedido.qtd else 0 end) 'devolvido',
			sum(tb_pedido.qtd) as tot,
			sum(tb_pedido.qtd * tb_pedido.vlunit) as vltot,
			#sexo
			sum(case when tb_cliente.sexo = 'M' then 1 else 0 end) 'masculino',
			sum(case when tb_cliente.sexo = 'F' then 1 else 0 end) 'feminino',
			sum(case when tb_cliente.sexo not in ('F', 'M') then 1 else 0 end) 'sexonaoinformado',
			#idade
			sum( case when (YEAR(CURDATE())-YEAR(tb_cliente.dtnascimento)) - (RIGHT(CURDATE(),5) < RIGHT(tb_cliente.dtnascimento,5)) <= 20 then 1 else 0 end) as 'menos20',
			sum(
				case when (
					(YEAR(CURDATE())-YEAR(tb_cliente.dtnascimento)) - (RIGHT(CURDATE(),5) < RIGHT(tb_cliente.dtnascimento,5)) >= 21
					and
					(YEAR(CURDATE())-YEAR(tb_cliente.dtnascimento)) - (RIGHT(CURDATE(),5) < RIGHT(tb_cliente.dtnascimento,5)) <= 30
					) then 1 else 0 end
				) as '21a30',
			sum(
				case when (
				(YEAR(CURDATE())-YEAR(tb_cliente.dtnascimento)) - (RIGHT(CURDATE(),5) < RIGHT(tb_cliente.dtnascimento,5)) >= 31
				and
				(YEAR(CURDATE())-YEAR(tb_cliente.dtnascimento)) - (RIGHT(CURDATE(),5) < RIGHT(tb_cliente.dtnascimento,5)) <= 40
				) then 1 else 0 end
			) as '31a40',
			sum(
				case when (
				(YEAR(CURDATE())-YEAR(tb_cliente.dtnascimento)) - (RIGHT(CURDATE(),5) < RIGHT(tb_cliente.dtnascimento,5)) >= 41
				and
				(YEAR(CURDATE())-YEAR(tb_cliente.dtnascimento)) - (RIGHT(CURDATE(),5) < RIGHT(tb_cliente.dtnascimento,5)) <= 50
				) then 1 else 0 end
			) as '41a50',
			sum( case when (YEAR(CURDATE())-YEAR(tb_cliente.dtnascimento)) - (RIGHT(CURDATE(),5) < RIGHT(tb_cliente.dtnascimento,5)) >= 51 then 1 else 0 end) as 'maior50',
			sum( case when (tb_cliente.dtnascimento = 0 or tb_cliente.dtnascimento = '0000-00-00' or (tb_cliente.dtnascimento is null and tb_cliente.cdcliente is not null)) then 1 else 0 end) as 'idadenaoinformada'
		from
			tb_oferta 
			left join tb_pedido on tb_oferta.cdoferta = tb_pedido.cdoferta
			left join tb_sitpedido on tb_pedido.cdsitpedido = tb_sitpedido.cdsitpedido
			left join tb_cliente on tb_pedido.cdcliente = tb_cliente.cdcliente
		where
			tb_oferta.cdoferta = '$cdoferta'
		group by
			tb_oferta.cdoferta,
			tb_oferta.nmoferta,
			dtinicio,
			dttermino,
			dtvalidade,
			dtliberacaocupom,
			tb_oferta.vloriginal,
			tb_oferta.vlpromocional
			order by
			tb_oferta.dtinicio desc,
			tb_oferta.dttermino desc,
			tb_oferta.nmoferta";
$sql = mysql_query($sql, $con);
while ($res = mysql_fetch_assoc($sql)) {
	array_walk($res, 'array_var_show');
	*/

//dados da oferta
$sql = "select 
			tb_oferta.cdoferta,
			tb_oferta.nmoferta,
			date_format(tb_oferta.dtinicio, '%d/%m/%Y %H:%i:%s') as dtinicio,
			date_format(tb_oferta.dttermino, '%d/%m/%Y %H:%i:%s') as dttermino,
			date_format(tb_oferta.dtvalidade, '%d/%m/%Y') as dtvalidade,
			date_format(tb_oferta.dtliberacaocupom, '%d/%m/%Y') as dtliberacaocupom,
			tb_oferta.vloriginal,
			tb_oferta.vlpromocional
		from tb_oferta
		where tb_oferta.cdoferta = '$cdoferta'";
$sql = mysql_query($sql, $con);
$res_oferta = mysql_fetch_assoc($sql);

//dados dos pedidos
$sql = "select sum(case when tb_sitpedido.sgsitpedido = 'AP' then tb_pedido.qtd else 0 end) 'aguardando_pagto',
			sum(case when tb_sitpedido.sgsitpedido = 'EA' then tb_pedido.qtd else 0 end) 'em_analise',
			sum(case when tb_sitpedido.sgsitpedido = 'A' then tb_pedido.qtd else 0 end) 'aprovado',
			sum(case when tb_sitpedido.sgsitpedido = 'C' then tb_pedido.qtd else 0 end) 'cancelado',
			sum(case when tb_sitpedido.sgsitpedido = 'E' then tb_pedido.qtd else 0 end) 'expirado',
			sum(case when tb_sitpedido.sgsitpedido = 'D' then tb_pedido.qtd else 0 end) 'devolvido',
			sum(tb_pedido.qtd) as tot,
			sum(tb_pedido.qtd * tb_pedido.vlunit) as vltot
		from 
			tb_pedido, tb_sitpedido
		where tb_pedido.cdsitpedido = tb_sitpedido.cdsitpedido and tb_pedido.cdoferta = '$cdoferta'";
$sql = mysql_query($sql, $con);
$res_pedido = mysql_fetch_assoc($sql);
?>
<div class="oferta" style="width:500px; margin:40px auto;padding:15px;">
<div class="titulo" style="margin-bottom:15px; font-size:16px;">
<?php echo $res_oferta['nmoferta']; ?>
</div>
<table class="table" style="width:100%;">
	<tr>
		<th colspan="2">Dados da oferta</th>
	</tr>
	<tr>
		<td>Início</td>
		<td><?php echo $res_oferta['dtinicio']; ?></td>
	</tr>
	<tr>
		<td>Término</td>
		<td><?php echo $res_oferta['dttermino']; ?></td>
	</tr>
	<tr>
		<td>Validade do cupom</td>
		<td><?php echo $res_oferta['dtvalidade']; ?></td>
	</tr>
	<tr>
		<td>Dt. Liberação do cupom</td>
		<td><?php echo $res_oferta['dtliberacaocupom']; ?></td>
	</tr>
	<tr>
		<td>Valor original</td>
		<td>R$<?php echo number_show($res_oferta['vloriginal']); ?></td>
	</tr>
	<tr>
		<td>Valor promocional</td>
		<td>R$<?php echo number_show($res_oferta['vlpromocional']); ?></td>
	</tr>
	<tr style="color:green;">
		<td>Total das vendas (Somente pedidos aprovados)</td>
		<td>R$<?php echo ($res_pedido['aprovado'] * $res_oferta['vlpromocional']);?></td>
	</tr>
</table>

<table class="table" style="width:100%;">
	<tr>
		<th colspan="2">Totalização de pedidos por status de pagamento</th>
	</tr>
	<tr>
	<th>Status</th>
		<th>Qtd.</th>
	</tr>
	<tr>
		<td>Aprovado</td>
	<td style="text-align:center;"><?php echo (int)$res_pedido['aprovado']; ?></td>
	</tr>
	<tr>
		<td>Aguardando Pagto</td>
	<td style="text-align:center;"><?php echo (int)$res_pedido['aguardando_pagto']; ?></td>
	</tr>
	<tr>
		<td>Em análise</td>
	<td style="text-align:center;"><?php echo (int)$res_pedido['em_analise']; ?></td>
	</tr>
	<tr>
		<td>Cancelado</td>
	<td style="text-align:center;"><?php echo (int)$res_pedido['cancelado']; ?></td>
	</tr>
	<tr>
		<td>Expirado</td>
	<td style="text-align:center;"><?php echo (int)$res_pedido['expirado']; ?></td>
	</tr>
	<tr>
		<td>Devolvido</td>
	<td style="text-align:center;"><?php echo (int)$res_pedido['devolvido']; ?></td>
	</tr>
	<tr>
		<td style="text-align:right; padding-right:10px;"><strong>Total</strong></td>
		<td style="text-align:center;"><strong><?php echo (int)$res_pedido['tot']; ?></strong></td>
	</tr>
</table>

<table class="table" style="width:100%;">
	<tr>
	<th colspan="3">Perfil dos compradores</th>
	</tr>
	<tr>
	<td valign="top" style="width:33.33%;">
		<!-- SEXO -->
		<?php
		$sql = "select distinct tb_cliente.cdcliente, tb_cliente.sexo, tb_cliente.dtnascimento
				from tb_cliente, tb_pedido
				where tb_cliente.cdcliente = tb_pedido.cdcliente and tb_pedido.cdoferta = '$cdoferta'";
		$sql = mysql_query($sql, $con);
		
		//sexo
		$m = 0;
		$f = 0;
		$n = 0;
		
		//idade
		$menos20 = 0;
		$i21a30 = 0;
		$i31a40 = 0;
		$i41a50 = 0;
		$imaior50 = 0;
		$idadenaoinformada = 0;
		
		while ($res = mysql_fetch_assoc($sql)) {
			if ($res['sexo'] == 'M') {
				$m++;
			}
			else if ($res['sexo'] == 'F') {
				$f++;
			}
			else {
				$n++;
			}
			
			//calculo de idade
			if (trim($res['dtnascimento']) == '' or trim($res['dtnascimento']) == '0000-00-00') {
				$idadenaoinformada++;
			}
			else if (valida_data($res['dtnascimento'])) {
				$data = explode('-', $res['dtnascimento']);
				$idade = calcIdade($data[2], $data[1], $data[0]);
				
				if ($idade <= 20) {
					$menos20++;
				}
				else if ($idade >= 21 and $idade <= 30) {
					$i21a30++;
				}
				else if ($idade >= 31 and $idade <= 40) {
					$i31a40++;
				}
				else if ($idade >= 41 and $idade <= 50) {
					$i41a50++;
				}
				else if ($idade >= 51) {
					$imaior50++;
				}
				else {
					$idadenaoinformada++;
				}
			}
			else {
				$idadenaoinformada++;
			}
		}
		?>
		<table class="table" style="width:100%;">
			<tr>
				<th>Sexo</th>
				<th>Qtd.</th>
			</tr>
			<tr>
				<td>Masculino</td>
				<td style="text-align:center;"><?php echo $m; ?></td>
			</tr>
			<tr>
				<td>Feminino</td>
				<td style="text-align:center;"><?php echo $f; ?></td>
			</tr>
			<tr>
				<td>Não informado</td>
				<td style="text-align:center;"><?php echo $n; ?></td>
			</tr>
			<tr>
				<td style="text-align:right;"><strong>Total</strong></td>
				<td style="text-align:center;"><?php echo ($n + $f + $m); ?></td>
			</tr>
		</table>
		<!-- FIM SEXO -->
	</td>
	<td style="width:33.33%;" valign="top">
		<!-- IDADE -->
		<table class="table" style="width:100%;">
			<tr>
				<th>Idade</th>
				<th>Qtd.</th>
			</tr>
			<tr>
				<td>Menos de 20</td>
				<td style="text-align:center;"><?php echo $menos20; ?></td>
			</tr>
			<tr>
				<td>21 a 30</td>
				<td style="text-align:center;"><?php echo $i21a30; ?></td>
			</tr>
			<tr>
				<td>31 a 40</td>
				<td style="text-align:center;"><?php echo $i31a40; ?></td>
			</tr>
			<tr>
				<td>41 a 50</td>
				<td style="text-align:center;"><?php echo $i41a50; ?></td>
			</tr>
			<tr>
				<td>acima de 50</td>
				<td style="text-align:center;"><?php echo $imaior50; ?></td>
			</tr>
			<tr>
				<td>Não informado</td>
				<td style="text-align:center;"><?php echo $idadenaoinformada; ?></td>
			</tr>
			<tr>
				<td style="text-align:right;"><strong>Total</strong></td>
				<td style="text-align:center;"><?php echo ($menos20 + $i21a30 + $i31a40 +$i41a50 + $imaior50 + $idadenaoinformada); ?></td>
			</tr>
		</table>
		<!-- FIM IDADE -->
	</td>
	<td valign="top" style="width:33.33%;">
	<!-- Cidade -->
		<table class="table" style="width:100%;">
			<tr>
				<th>UF</th>
				<th>Cidade</th>
				<th>Qtd.</th>
			</tr>
			<?php
			$sql_cidade = "select
								tb_cidade.nmcidade,
								tb_estado.sgestado,
								count(distinct tb_cliente.cdcliente) as totcliente
							from
								tb_cidade,
								tb_estado,
								tb_pedido,
								tb_cliente
							where
								tb_pedido.cdoferta = '$cdoferta' and
								tb_pedido.cdcliente = tb_cliente.cdcliente and
								tb_cliente.cdcidade = tb_cidade.cdcidade and
								tb_cidade.cdestado = tb_estado.cdestado
								group by
								tb_cidade.nmcidade,
								tb_estado.sgestado
							order by
								totcliente desc,
								tb_estado.sgestado,
								tb_cidade.nmcidade";
			$sql_cidade = mysql_query($sql_cidade, $con);
			$soma = 0;
			while ($res_cidade = mysql_fetch_assoc($sql_cidade)) {
				echo '<tr>';
				echo '<td>' , $res_cidade['sgestado'] , '</td>';
				echo '<td>' , $res_cidade['nmcidade'] , '</td>';
				echo '<td>' , (int)$res_cidade['totcliente'] , '</td>';
				echo '</tr>';
				$soma += $res_cidade['totcliente'];
			}
			
			$sql_cidade = "select count(distinct tb_cliente.cdcliente) as totcliente
							from tb_cliente, tb_pedido
							where tb_cliente.cdcliente = tb_pedido.cdcliente and
							      tb_pedido.cdoferta = '$cdoferta' and      
							      (tb_cliente.cdcidade is null or tb_cliente.cdcidade in ('', 0))";
			$sql_cidade = mysql_query($sql_cidade, $con);
			$sql_cidade = mysql_fetch_assoc($sql_cidade);
			echo '<td colspan="2">Não informado</td>';
			echo '<td>' , $sql_cidade['totcliente'] , '</td>';
			$soma += $sql_cidade['totcliente'];
			echo '</tr>';
			?>
			<tr>
				<td style="text-align:right;"><strong>Total</strong></td>
				<td style="text-align:center;"><?php echo $soma; ?></td>
			</tr>
		</table>
	<!-- fim cidade -->
	</td>
	</tr>
</table>
</div>
<?php
//}
