<?php
/**
 * Funcѕes responsсveis por efetuar log das operaчѕes
 * - Criado em 08/04/2011
 * 
 * @author Niury Martins - http://niurymartins.com.br
 * @package includes
 */

/**
 * Realiza o log da tabela de pedidos
 *
 * @param int $cdpedido
 * @param int $cdusuario_log
 * @param char $tpoperacao
 * @return boolean
 */
function logPedido($cdpedido, $cdusuario_log, $tpoperacao) {
	global $con;
	
	$sql = "select * 
			from tb_pedido
			where cdpedido = '$cdpedido'";
	$sql = mysql_query($sql, $con);
	if (mysql_num_rows($sql)) {
		extract(mysql_fetch_assoc($sql));
		$sql = "insert into log_pedido
				(cdpedido, cdcliente, cdoferta, qtd,
				 vlunit, dtpedido, cdsitpedido, cupom_enviado, 
				 cupom, observacao, confpedido, 
				 cdcidade, codtransacao, cdusuariolog, tpoperacao)
				values
				('$cdpedido', '$cdcliente', '$cdoferta', '$qtd', 
				 '$vlunit', '$dtpedido', '$cdsitpedido', '$cupom_enviado',
				 '$cupom', '$observacao', '$confpedido',
				 '$cdcidade', '$codtransacao', '$cdusuario_log', '$tpoperacao')";
		return mysql_query($sql, $con);
	}
	
	return false;
}

/**
 * Realiza o log das operaчѕes feitas na tabela de clientes
 *
 * @param int $cdcliente
 * @param int $cdusuario_log
 * @param char $tpoperacao
 * @return boolean
 */
function logCliente($cdcliente, $cdusuario_log, $tpoperacao) {
	global $con;
	$sql = "select * from tb_cliente where cdcliente = '$cdcliente'";
	$sql = mysql_query($sql, $con);
	if (mysql_num_rows($sql)) {
		extract(mysql_fetch_assoc($sql));
		$sql = "insert into log_cliente
				(cdcliente, nmcliente, email, recebernews, 
				 senha, cdcidade, endereco, nmbairro, 
				 cep, complemento, sexo, dtnascimento, cdestadocivil, 
				 telcel, telresid, dtcadastro, num, 
				 ativo, excluido, observacao, idnovasenha, cpf, 
				 receber_sms, cdusuariolog, tpoperacao)
				values
				('$cdcliente', '$nmcliente', '$email', '$recebernews',
				 '$senha', '$cdcidade', '$endereco', '$nmbairro',
				 '$cep', '$complemento', '$sexo', '$dtnascimento', '$cdestadocivil',
				 '$telcel', '$telresid', '$dtcadastro', '$num', 
				 '$ativo', '$excluido', '$observacao', '$idnovasenha', '$cpf',
				 '$receber_sms', '$cdusuario_log', '$tpoperacao')";
		return mysql_query($sql, $con);
	}
	
	return false;
}

/**
 * Realiza o log de operaчѕes na tabela de utilizaчуo de pedidos
 *
 * @param int $cdutilizacao
 * @param int $cdusuario_log
 * @param char $tpoperacao
 * @return boolean
 */
function logUtilizacao_pedido($cdutilizacao, $cdusuario_log, $tpoperacao) {
	global $con;
	$sql = "select * 
			from tb_utilizacao_pedido
			where cdutilizacao = '$cdutilizacao'";
	$sql = mysql_query($sql, $con);
	if (!mysql_num_rows($sql)) {
		return false;
	}
	
	$sql = mysql_fetch_assoc($sql);
	extract($sql);
	
	$sql = "insert into log_utilizacao_pedido
			(cdutilizacao, cdpedido, qtdutilizada, 
			 dtutilizacao, dtinsercao, cdusuario, 
			 cdusuariolog, tpoperacao, observacao)
			values
			('$cdutilizacao', '$cdpedido', '$qtdutilizada',
			 '$dtutilizacao', '$dtinsercao', '$cdusuario',
			 '$cdusuario_log', '$tpoperacao', '$observacao')";
	
	return mysql_query($sql, $con);
}