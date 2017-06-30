<?php 
/**
 * script de exclusão de cliente
 * - Criado em 11/01/2011
 * 
 * @author Niury Martins - http://niurymartins.com.br
 */

if (isset($_GET['cdcliente'])) {

	//caso seja usuário do tipo franquia
	if (somenteFranquia()) {
		$where = " and tb_cliente.cdcidade in (" . getCidade() . ")";
	}
	else {
		$where = '';
	}

	$cdcliente = (int)$_GET['cdcliente'];

	if ($cdcliente) {
		logCliente($cdcliente, $_SESSION['logado']['usuario']['cdusuario'], 'D');
		$sql = "delete from tb_cliente where cdcliente = '$cdcliente' $where";
		if (!mysql_query($sql, $con)) {
			$sql = "update tb_cliente
					set excluido = 'S'
					where cdcliente = '$cdcliente' $where";
			mysql_query($sql, $con);
		}
		
		set_session_msg('cadcliente', 'ok', MSG_OK);
	}
	else {
		set_session_msg('cadcliente', 'error', 'O cliente selecionado não foi encontrado');
	}
}

$pag =  isset($_GET['pag']) ? '&' . $_GET['pag'] : '' ;
header('location:' . montalink('lista-cliente', '&') . $pag);