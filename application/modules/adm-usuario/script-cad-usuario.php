<?php

//caso seja salvar dados do usuário
if (isset($_POST['salvar_dados'])) {

	$msg = '';
	$_SESSION['post'] = $_POST;
	array_walk($_POST, 'array_insert_db');
	extract($_POST);
	$nmusuario = strtoupper($nmusuario);
	$login = char_login($login);

	//faz as validações
	//nmusuario
	if ($nmusuario == '') {
		set_msg($msg, 'Favor preencher o nome do usuario');
	}

	//e-mail
	if($email == '') {
		set_msg($msg, 'Favor preencher o e-mail do usuário');
	}

	//login
	if ($login == '') {
		set_msg($msg, 'Favor preencher o login do usuário');
	}
	else if (strlen($login) < 5 or strlen($login) > 16) {
		set_msg($msg, 'O login deve conter entre 5 e 16 caracteres');
	}
	else {

		$aux = isset($cdusuario) ? ' and cdusuario <> ' . $cdusuario : '' ;
		$sql = "select * from sis_usuario where login = '$login' $aux";
		$sql = mysql_query($sql, $con_sma);
		if (mysql_num_rows($sql)) {
			set_msg($msg, 'O login informado já existe');
		}
	}

	//caso não tenha tido erros
	if ($msg == '') {

		//caso seja alteração
		if (isset($cdusuario)) {

			$sql = "update sis_usuario set
						nmusuario = '$nmusuario',
						email = '$email',
						login = '$login',
						alterar_senha='$alterar_senha'
					where cdusuario = '$cdusuario'";
		}
		//caso seja inserção
		else {
			$sql = "insert into sis_usuario (nmusuario, email, login, alterar_senha, cdusuariocad) values
					('$nmusuario', '$email', '$login', '$alterar_senha', '{$_SESSION['logado']['usuario']['cdusuario']}')";
		}

		if (mysql_query($sql, $con_sma)) {
			set_msg($msg, MSG_OK);
			set_session_msg('msg-salvar-dados', 'ok', $msg);
			if (!isset($cdusuario)) {
				$cdusuario = mysql_insert_id($con_sma);
			}
		}
		else {
			set_msg($msg, MSG_ERROR);
			set_session_msg('msg-salvar-dados', 'error', $msg);
		}
	}
	else {
		set_session_msg('msg-salvar-dados', 'alert', $msg);
	}
}

//caso seja adicionar tipo de usuário
if (isset($_POST['salvar_tpusuario'])) {
	array_walk($_POST, 'array_insert_db');
	extract($_POST);

	if ($cdtpusuario == 0 or $cdtpusuario == '') {
		set_session_msg('msg-salvar-tpusuario', 'alert', 'Favor selecionar o tipo de usuário');
	}
	else {

		$sql = "insert into sis_usuario_tpusuario (cdusuario, cdtpusuario) values
			($cdusuario, $cdtpusuario)";
		if (mysql_query($sql, $con_sma)) {
			set_session_msg('msg-salvar-tpusuario', 'ok', MSG_OK);
		}
		else {
			set_session_msg('msg-salvar-tpusuario', 'error', MSG_ERROR . '<br /><br />' . mysql_error($con));
		}
	}
}

//caso seja excluir um tipo de usuário do usuário
if (isset($_GET['excluir-tpusuario'])) {
	array_walk($_GET, 'array_insert_db');
	if (isset($_GET['cdusuario']) and isset($_GET['cdtpusuario'])) {
		extract($_GET);
		$sql = "delete from sis_usuario_tpusuario
				where cdusuario = '$cdusuario' and
					  cdtpusuario = '$cdtpusuario'";
		if (mysql_query($sql, $con_sma)) {
			
			$sql = "select sgtpusuario 
					from sis_tpusuario 
					where cdtpusuario = '$cdtpusuario'";
			$sql = mysql_query($sql, $con);
			$sql = mysql_fetch_assoc($sql);
						
			//exclui as cidades caso o tipo de usuário excluido seja franquia
			if ($sql['sgtpusuario'] == 'FR') {
				$sql = "delete from sis_usuario_cidade where cdusuario = '$cdusuario'";
				mysql_query($sql,$con);
			}
			
			//exclui as empresas do usuário caso o tipo de usuário excluido seja
			//logista
			if ($sql['sgtpusuario'] == 'LO') {
				$sql = "delete from sis_usuario_empresa where cdusuario = '$cdusuario'";
				mysql_query($sql, $con);
			}
			
			set_session_msg('msg-salvar-tpusuario', 'ok', MSG_OK);
		}
		else {
			set_session_msg('msg-salvar-tpusuario', 'error', MSG_ERROR);
		}
	}
}

//salvar senha
if (isset($_POST['salvar-senha'])) {

	extract($_POST);
	array_walk($_POST, 'array_insert_db');
	extract($_POST);

	$senha = $senha;
	$rsenha = $rsenha;
	$msg = '';

	if($senha != '' and $rsenha != '') {
		$senha = char_senha($senha);
		$rsenha = char_senha($rsenha);

		if($senha != $rsenha) {
			set_msg($msg, 'A confirmação da nova senha está incorreta');
		}

		if ($msg == '') {
			$sql = "update sis_usuario set senha='" . $senha . "'
					where cdusuario='$cdusuario'";
			if($sql = mysql_query($sql, $con_sma)) {
				set_session_msg('msg-salvar-senha', 'ok', MSG_OK);
			}
			else {
				set_session_msg('msg-salvar-senha', 'error', MSG_ERROR);
			}
		}
		else {
			set_session_msg('msg-salvar-senha', 'alert', $msg);
		}
	}
	else {
		set_session_msg('msg-salvar-senha', 'alert', 'Favor preencher corretamente os campos');
	}
}

//caso seja excluir um usuário
if (isset($_GET['excluir-usuario'])) {
	array_walk($_GET, 'array_insert_db');
	extract($_GET);
	if (isset($cdusuario)) {

		$sql = "delete from sis_usuario where cdusuario = '$cdusuario'";
		if (mysql_query($sql, $con_sma)) {
			set_session_msg('msg-excluir-usuario', 'ok', MSG_DELETE);
		}
		else {
			set_session_msg('msg-excluir-usuario', 'error', mysql_error($con_sma));
		}
	}
	else {
		set_session_msg('msg-excluir-usuario', 'error', MSG_ERROR);
	}

	header('location: ' . montalink('lista-usuarios-cadastrados', '&'));
	exit(1);
}

//caso seja incluir uma empresa para o usuário
if (isset($_POST['salvarempresa'])) {
	array_walk($_POST, 'array_insert_db');
	extract($_POST);
	if (!$cdusuario or !$cdempresa) {
		set_session_msg('msg-salvar-empresa', 'alert', 'Favor selecionar a empresa');
	}
	else {
		$sql = "insert into sis_usuario_empresa (cdusuario, cdempresa)
				values 
				('$cdusuario', '$cdempresa')";
		if (mysql_query($sql, $con)) {
			set_session_msg('msg-salvar-empresa', 'ok', MSG_OK);
		}
		else {
			set_session_msg('msg-salvar-empresa', 'error', mysql_error($con));
		}
	}
}

//caso seja excluir a empresa de um usuário
if (isset($_GET['excluir-empresa'])) {
	array_walk($_GET, 'array_insert_db');
	$cdusuario = (int)$_GET['cdusuario'];
	$cdempresa = (int)$_GET['cdempresa'];
	if ($cdusuario and $cdempresa) {
		$sql = "delete from sis_usuario_empresa
				where cdusuario = '$cdusuario' and
					  cdempresa = '$cdempresa'";
		if (mysql_query($sql, $con)) {
			set_session_msg('msg-salvar-empresa', 'ok', MSG_DELETE);
		}
		else {
			set_session_msg('msg-salvar-empresa', 'error', mysql_error($con));
		}
	}
}

//caso seja incluir uma cidade para o usuário
if (isset($_POST['salvarcidade'])) {
	array_walk($_POST, 'array_insert_db');
	extract($_POST);
	if (!$cdusuario or !$cdcidade) {
		set_session_msg('msg-salvar-cidade', 'alert', 'Favor selecionar a cidade');
	}
	else {
		$sql = "insert into sis_usuario_cidade (cdusuario, cdcidade)
				values 
				('$cdusuario', '$cdcidade')";
		if (mysql_query($sql, $con)) {
			set_session_msg('msg-salvar-cidade', 'ok', MSG_OK);
		}
		else {
			set_session_msg('msg-salvar-cidade', 'error', mysql_error($con));
		}
	}
}

//caso seja excluir a cidade de um usuário
if (isset($_GET['excluir-cidade'])) {
	array_walk($_GET, 'array_insert_db');
	if (isset($_GET['cdusuario']) and isset($_GET['cdcidade'])) {
		extract($_GET);
		$sql = "delete from sis_usuario_cidade
				where cdusuario = '$cdusuario' and
					  cdcidade = '$cdcidade'";
		if (mysql_query($sql, $con)) {
			set_session_msg('msg-salvar-cidade', 'ok', MSG_DELETE);
		}
		else {
			set_session_msg('msg-salvar-cidade', 'error', mysql_error($con));
		}
	}
}

//faz o redirecionamento para o fomulário
if (isset($cdusuario)) {
	header('location: ' . montalink('form-cad-usuario', '&') . '&cdusuario=' . $cdusuario);
}
else {
	header('location: ' . montalink('form-cad-usuario', '&'));
}