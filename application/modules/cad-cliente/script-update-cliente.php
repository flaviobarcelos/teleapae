<?php
/**
 * script de edição de cliente
 * - Criado em 11/01/2011
 * 
 * @author Niury Martins - http://niurymartins.com.br
 */

if (isset($_POST['salvar'])) {

	include_once(CLASSES . '/class.ValidaCadCliente.php');
	$_SESSION['postcadcliente'] = $_POST;
	extract($_POST);

	$validacao = new ValidaCadCliente($_POST);
	$validacao->executaValidacao();
	if (!$validacao->obtemValidacao()) {
		set_session_msg('cadcliente', 'alert', $validacao->obtemErros());
	}
	else {
		logCliente($cdcliente, $_SESSION['logado']['usuario']['cdusuario'], 'U');
		$sql = "update tb_cliente set
				nmcliente = '{$validacao->nmcliente}', 
				email = '{$validacao->email}', 
				recebernews = '{$validacao->recebernews}', 
				receber_sms = '{$validacao->receber_sms}',
				cdcidade = '{$validacao->cdcidade}', 
				endereco = '{$validacao->endereco}',
				nmbairro = '{$validacao->nmbairro}', 
				cep = '{$validacao->cep}', 
				complemento = '{$validacao->complemento}', 
				sexo = '{$validacao->sexo}', 
				dtnascimento = '{$validacao->dtnascimento}', 
				cdestadocivil = {$validacao->cdestadocivil},
				telcel = '{$validacao->telcel}',
				telresid = '{$validacao->telresid}', 
				num = '{$validacao->num}', 
				ativo = '{$validacao->ativo}', 
				observacao = '{$validacao->observacao}'
				where 
					cdcliente = '{$validacao->cdcliente}'";
		if (mysql_query($sql, $con)) {
			
			if ($validacao->senha) {
				$sql = "update tb_cliente set
						senha = '{$validacao->senha}'
						where cdcliente = '{$validacao->cdcliente}'";
				mysql_query($sql, $con);
			}
			
			$cdcliente = $validacao->cdcliente;
			set_session_msg('cadcliente', 'ok', MSG_ALTERACAO);
		}
		else {
			set_session_msg('cadcliente', 'error', mysql_error($con));
		}
	}
}

$aux = $cdcliente ? '&cdcliente=' . $cdcliente : '' ;
header('location: ' . montalink('form-cad-cliente', '&') . $aux);