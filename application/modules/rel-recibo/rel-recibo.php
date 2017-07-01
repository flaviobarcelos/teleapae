<?php
//phpinfo();
//exit;
/**
 * impress�o dos recibos
 */

setlocale(LC_ALL, "pt_BR", "ptb");
ini_set("memory_limit","2048M");

//tratamento de recebimento dos dados
array_walk_recursive($_REQUEST, 'array_insert_db');
extract($_REQUEST);
$where = '';
$erro = false;
$msg = '';

if (!$cddoacao and (!$dtrecini or !$dtrecfim)) {
	$msg = 'Favor informar c�digo do recibo ou per�odo de recebimento';
	$erro = true;
}
else if ($dtrecini and $dtrecfim) {
	$dtrecini = inverte_formato_data($dtrecini);
	$dtrecfim = inverte_formato_data($dtrecfim);

	if ($dtrecini > $dtrecfim) {
		$msg = 'A data de recebimento inicial n�o pode ser maior que a data final';
		$erro = true;
	}
}

if ($erro) {
	require(INCLUDES . '/inc.header.php');
	echo msg('alert', $msg);
	require(INCLUDES . '/inc.footer.php');
	exit();
}

//realiza a consulta no banco
$where = '';
if ($cddoacao) {
	$where .= " and tb_doacao.cddoacao in (" . $cddoacao . ") ";
}

if ($dtrecini and $dtrecfim) {
	$where .= " and tb_doacao.dtrec between '$dtrecini' and '$dtrecfim' ";
}

if ($cddoador) {
	$where .= " and tb_doacao.cddoador = '$cddoador' ";
}

if ($cancelado) {
	$where .= " and tb_doacao.cancelado = '$cancelado' ";
}

if ($cdcidade) {
	$where .= " and tb_doador.cdcidade = '$cdcidade' ";
}

if (count($nmbairro)) {
	$where .= " and tb_doador.nmbairro in ( ";
	$x = 0;

	foreach ($nmbairro as $res) {
		$where .= $x > 0 ? ',' : '' ;
		$where .= " '$res' ";
		$x++;
	}

	$where .= " ) ";
}

if (!$ordenacao) {
	$ordenacao = ' tb_doacao.cddoacao';
}

$sql = "select
		       tb_doador.*,
		       tb_doacao.cddoacao,
		       tb_doacao.dtrec,
		       tb_doacao.obsrecdoacao,
		       tb_doacao.vldoacao,
		       tb_tpdoador.*,
		       tb_cidade.nmcidade,
		       tb_estado.sgestado
		from
		    tb_doacao
		    inner join tb_doador on tb_doador.cddoador = tb_doacao.cddoador
		    inner join tb_cidade on tb_doador.cdcidade = tb_cidade.cdcidade
		    inner join tb_estado on tb_cidade.cdestado = tb_estado.cdestado
		    inner join tb_tpdoador on tb_doador.cdtpdoador = tb_tpdoador.cdtpdoador
		    left join sis_usuario on tb_doador.cdusuario = sis_usuario.cdusuario
		where 
			tb_doador.ativo = 'S' and 
			tb_doacao.excluido = 'N' $where
		order by " . strtolower($ordenacao);

//die(mostra_array($sql));
$sql = mysql_query($sql, $con);
if (!mysql_num_rows($sql)) {
	require(INCLUDES . '/inc.header.php');
	echo msg('alert', 'Nenhum recibo encontrado');
	require(INCLUDES . '/inc.footer.php');
	exit();
}
?>

<html>
	<head>
		<title>IMPRESS�O DE RECIBOS</title>
		<style type="text/css" media="all">
		* {font:normal 10px tahoma; margin:0; padding:0;}
		table tr td, table tr {margin:0; padding:0;}
		strong {font-weight:bold;}
		</style>
	</head>
	<body style="margin:0; padding:0;">
		<?php

		while ($res = mysql_fetch_assoc($sql)) {
			array_walk($res, 'array_var_show');
			//verifica se e niver so doador
			$dtaux = date('m', strtotime($res['dtrec']));


			if ($res['dtniverresp']) {
				$dtniverresp = date('m', strtotime($res['dtniverresp']));
			}
			else {
                $dtniverresp = '';
            }

            /*
			if ($res['dtniverconjuge']) {
				$dtniverconjuge = date('m', strtotime($res['dtniverconjuge']));
			}
			else {
                 $dtniverconjuge = '';
            }
            */

			if ($dtniverresp == $dtaux) {
				$res['obsrecibo'] .= '  <strong>***FELIZ ANIVERS�RIO!***</strong>';
			}

			//cpf/cnpj
			if ($res['sgtppessoa'] == 'F') {
				$cpf = $res['cpf'];
			}
			else {
				$cpf = $res['cnpj'];
			}

			if (!$cpf) {
				$cpf = '';
			}
			else {
				$cpf = 'CNPJ/CPF n� ' . $cpf . ',';
			}

			//telefone
			$tel = $res['telefone1'];
			if ($res['telefone2']) {
				$tel .= ' / ' . $res['telefone2'];
			}

			if ($res['telefone3']) {
				$tel .= ' / ' . $res['telefone3'];
			}

			if ($tel) {
				$tel = ', telefone(s) ' . $tel;
			}

			//vldoacao
			if ($res['vldoacao'] <= 0) {
				$vldoacao = '_____________';
			}
			else {
				$vldoacao = 'R$ ' . number_show($res['vldoacao']);
			}

			//define a obs que vai para o recibo
			//if ($res['obsrecdoacao']) {
			//$obs = $res['obsrecdoacao'];
			//}
			//else {
			$obs = $res['obsrecibo'];
			//$res['obsrecibo'] = '';
			//}

			for ($x = 0; $x <= 1; $x++) {


				if ($x > 0) {
					echo '<table border="0" style="height:5%; margin:25px 0 20px 0; width:100%;">
						      <tr>
						          <td valign="middle" style="color:#AAAAAA;" align="center">&nbsp;</td>
						      </tr>
						  </table>';
				}


				echo '
				<table border="0" style="border:none; margin:0 auto; height:45%; padding:0; width:18cm;">
					<tr>
						<td style="width:100%; text-align:center;">
							<table border="0" style="border:2px solid; height:400px; padding:10px 15px; width:100%;">
								<tr>
									<td align="center" colspan="2"><strong style="font-size:14px;">ASSOCIA��O DE PAIS E AMIGOS DOS EXCEPCIONAIS DE IPATINGA</strong></td>
								</tr>
								<tr>
									<td style="padding-top:10px;" align="center">
										<img src="' . IMG .'/teleapae-rel.png" alt="APAE" style="width:60px;" />
									</td>
									<td style="padding-left:15px; text-align:center; font-size:12px;">
										Av. 26 de Outubro, 1595, B. Bela Vista, Ipatinga/MG - CEP 35.160-208<br />
										CNPJ 20.951.190/0001-30 - U.P. MUNICIPAL - LEI N� 649 de 19/07/79<br />
										U.P. ESTADUAL - LEI N� 7656 de 27/12/79 - U.P. FEDERAL DECRETO LEI N� 91.108 de 12/03/85<br />
										Registro CNSS: 23.002.006747/88-53 - TELEFONE: (31)3822-3502
									</td>
								</tr>
								<tr><td colspan="2" style="font-size:18px; font-weight: bold; text-align:center;">RECIBO N� ' . number_format($res['cddoacao'], 0, '', '.') . ' </td></tr>
								<tr>
									<td colspan="2" style="line-height:20px; font-size:14px;">
									Recebemos de <strong style="font-size:14px;">' . $res['nmresponsavel'] . '</strong>, ' . $cpf . ' residente na ' . $res['endereco'] . ', <strong style="font-size:14px;">' . $res['nmbairro'] . '</strong>, ' . $res['nmcidade'] . '/' . $res['sgestado'] . $tel . ', a quantia de <strong style="font-size:14px;">' . $vldoacao . '</strong>, referente � doa��o do dia <strong style="font-size:14px;">' . inverte_formato_data($res['dtrec'], '/') . '</strong>.
									<br /><br />
									Para clareza e devidos fins, firmamos o presente.
									<br /><br />
									Ipatinga, ' . date('d' , strtotime($res['dtrec'])) . ' de ' . getMesPtbr(date('m' , strtotime($res['dtrec']))) . ' de ' . date('Y' , strtotime($res['dtrec'])) . '
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<table border="0" style="width:100%; margin-top:30px;">
											<tr>
												<td align="center" style="font-weight:bold; font-size:14px;">
													______________________________
													<br />
													APAE de Ipatinga
												</td>
												<td align="center" style="font-weight:bold; font-size:14px;">
													______________________________
													<br />
													Mensageiro
												</td>
											</tr>
											<tr>
												<td style="font-weight:bold; font-size:16px;" align="left">' . $res['sgtpdoador'] . '</td>
												<td style="font-weight:bold; font-size:16px;" align="right">' . $res['cdusuario'] . '</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						<td>
						<tr>
							<td>
								<div style="height:35px;border:none">' . $obs . '<div>		
							</td>
						</tr>
				</tr>
				</table>';

               /*
				if ($x > 0) {
					echo '<table border="0" style="height:5%; margin:0 auto; width:100%;">
						      <tr>
						          <td valign="middle" style="color:#AAAAAA;" align="center">-------------------------------------------------------------------------------------------------------------------</td>
						      </tr>
						  </table>';
				}
				*/
			}
		}
		?>
	</body>
</html>
