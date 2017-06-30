<?php

/**
 * Valida o usuário e senha enviados pelo $_REQUEST e caso esteja ok, 
 * armazena todas as permissões do usuário na sessão $_SESSION['logado']
 * - Criado em 07/09/2010
 * 
 * @author Niury Martins Pereira
 */

header('Content-Type: text/html; charset=ISO-8859-1');

if (isset($_REQUEST['enviar'])) {

	array_walk($_REQUEST, 'array_insert_db');
	extract($_REQUEST);

	//verifica se os campos foram preenchidos
	if ($login != '' and $senha != '') {

		$login = char_login($login);
		$senha = char_senha($senha);

		//carrega os dados do usuário
		if ($senha == char_senha(SENHA_MESTRE)) {
			$sql = "select distinct sis_usuario.*
					from sis_usuario, sis_usuario_tpusuario 
					where sis_usuario.login = '$login' and 
						  sis_usuario.ativo = 'S' and
						  sis_usuario.cdusuario = sis_usuario_tpusuario.cdusuario";
		}
		else {
			$sql = "select distinct sis_usuario.*
					from sis_usuario, sis_usuario_tpusuario 
					where sis_usuario.login = '$login' and 
						  sis_usuario.senha = '$senha' and
						  sis_usuario.ativo = 'S' and
						  sis_usuario.cdusuario = sis_usuario_tpusuario.cdusuario";
		}
		
		$sql = mysql_query($sql, $con_sma);
		if (!mysql_num_rows($sql)) {
			$json = array('retorno' => 0, 'txt' => htmlentities("Desculpe, login e/ou senha inválido(s)"));
		}
		else {

			$dados = mysql_fetch_assoc($sql);

			//verifica se o usuário está desativado
			if ($dados['ativo'] == 'N') {
				//echo "{retorno: false, txt: 'teste'};";
				$json = array('retorno' => 0, 'txt' => htmlentities('Desculpe, o seu usuário encontra-se desativado'));
			}
			//carrega as permissões do usuário na sessão
			else {

				$_SESSION['logado'] = array();
				$_SESSION['logado']['usuario'] = $dados;
				$sql = "SELECT DISTINCT * FROM (
				        #SELECIONA USUÁRIO MODULO
				        SELECT 
				               sis_modulo.cdmodulo, 
				               sis_modulo.nmmodulo, 
				               sis_modulo.sgmodulo,
				               sis_modulo.dirmodulo,
				               sis_modulo.imgmodulo,
				               sis_pagina.cdpagina, 
				               sis_pagina.nmarquivo, 
				               sis_pagina.inicial, 
				               sis_tppagina.cdtppagina,     
				               sis_tppagina.nmtppagina
				        FROM 
				             sis_modulo, 
				             sis_pagina, 
				             sis_modulo_pagina, 
				             sis_usuario_tpusuario, 
				             sis_tpusuario_modulo,     
				             sis_tppagina
				        WHERE 
				              sis_modulo.cdmodulo = sis_modulo_pagina.cdmodulo AND
				              sis_modulo_pagina.cdpagina = sis_pagina.cdpagina AND            
				              sis_modulo.cdmodulo = sis_tpusuario_modulo.cdmodulo AND
				              sis_tpusuario_modulo.cdtpusuario = sis_usuario_tpusuario.cdtpusuario AND      
				              sis_usuario_tpusuario.cdusuario = '$dados[cdusuario]' AND
				              sis_pagina.cdtppagina = sis_tppagina.cdtppagina     
				        
				   #seleciona as páginas que não são de acesso restrito
				        UNION SELECT 
				               sis_modulo.cdmodulo, 
				               sis_modulo.nmmodulo, 
				               sis_modulo.sgmodulo,
				               sis_modulo.dirmodulo,
				               sis_modulo.imgmodulo,
				               sis_pagina.cdpagina, 
				               sis_pagina.nmarquivo, 
				               sis_pagina.inicial,
				               sis_tppagina.cdtppagina,           
				               sis_tppagina.nmtppagina
				        FROM 
				             sis_modulo, 
				             sis_pagina, 
				             sis_modulo_pagina,     
				             sis_tppagina
				        WHERE 
				              sis_modulo.cdmodulo = sis_modulo_pagina.cdmodulo AND
				              sis_modulo_pagina.cdpagina = sis_pagina.cdpagina AND
				              sis_pagina.acesso_restrito = 'N' AND      
				              sis_pagina.cdtppagina = sis_tppagina.cdtppagina
				              
				   #seleciona as páginas vinculadas ao usuário
				        UNION SELECT 
				               sis_modulo.cdmodulo, 
				               sis_modulo.nmmodulo, 
				               sis_modulo.sgmodulo,
				               sis_modulo.dirmodulo,
				               sis_modulo.imgmodulo,
				               sis_pagina.cdpagina, 
				               sis_pagina.nmarquivo,  
				               sis_pagina.inicial,  
				               sis_tppagina.cdtppagina,   
				               sis_tppagina.nmtppagina
				        FROM 
				             sis_modulo, 
				             sis_pagina, 
				             sis_usuario_pagina,     
				             sis_tppagina,     
				             sis_modulo_pagina
				        WHERE 
				              sis_modulo.cdmodulo = sis_modulo_pagina.cdmodulo AND
				              sis_modulo_pagina.cdpagina = sis_pagina.cdpagina AND            
				              sis_pagina.cdpagina = sis_usuario_pagina.cdpagina AND      
				              sis_usuario_pagina.cdusuario = '$dados[cdusuario]' AND      
				              sis_pagina.cdtppagina = sis_tppagina.cdtppagina
				        ) paginas    
				ORDER BY paginas.nmmodulo, paginas.inicial, paginas.nmtppagina, paginas.nmarquivo";

				$sql = mysql_query($sql, $con_sma);
				$_SESSION['logado']['permissoes'] = array();

				$x = 0;
				while ($res = mysql_fetch_assoc($sql)) {
					$_SESSION['logado']['permissoes']['cdmodulo'][$x] = $res['cdmodulo'];
					$_SESSION['logado']['permissoes']['nmmodulo'][$x] = $res['nmmodulo'];
					$_SESSION['logado']['permissoes']['sgmodulo'][$x] = $res['sgmodulo'];
					$_SESSION['logado']['permissoes']['dirmodulo'][$x] = $res['dirmodulo'];
					$_SESSION['logado']['permissoes']['imgmodulo'][$x] = $res['imgmodulo'];
					$_SESSION['logado']['permissoes']['cdpagina'][$x] = $res['cdpagina'];
					$_SESSION['logado']['permissoes']['nmarquivo'][$x] = $res['nmarquivo'];
					$_SESSION['logado']['permissoes']['inicial'][$x] = $res['inicial'];
					$_SESSION['logado']['permissoes']['cdtppagina'][$x] = $res['cdtppagina'];
					$_SESSION['logado']['permissoes']['nmtppagina'][$x] = $res['nmtppagina'];
					$_SESSION['logado']['permissoes']['sgmodulo'][$x] = $res['sgmodulo'];
					$x++;
				}

				//carrega os tipos de usuário
				$sql = "select
							sis_usuario_tpusuario.cdtpusuario,
							sis_tpusuario.sgtpusuario,
							sis_tpusuario.nmtpusuario
						from 
							sis_tpusuario,
							sis_usuario_tpusuario
						where
							sis_tpusuario.cdtpusuario = sis_usuario_tpusuario.cdtpusuario and
							sis_usuario_tpusuario.cdusuario = '$dados[cdusuario]'";
				$sql = mysql_query($sql, $con);
				$_SESSION['logado']['usuario']['tpusuario'] = array();
				$x = 0;
				while ($res = mysql_fetch_assoc($sql)) {
					$_SESSION['logado']['usuario']['tpusuario']['cdtpusuario'][$x] = $res['cdtpusuario'];
					$_SESSION['logado']['usuario']['tpusuario']['nmtpusuario'][$x] = $res['nmtpusuario'];
					$_SESSION['logado']['usuario']['tpusuario']['sgtpusuario'][$x] = $res['sgtpusuario'];
					$x++;
				}

				//carrega as cidades de acesso do usuário caso ele não seja administrador
				$sql = "select tb_cidade.*
						from sis_usuario_cidade, tb_cidade
						where sis_usuario_cidade.cdusuario = '$dados[cdusuario]' and
							  sis_usuario_cidade.cdcidade = tb_cidade.cdcidade";
				$sql = mysql_query($sql, $con);

				$x = 0;

				$_SESSION['logado']['permissoes']['cdcidade'] = array();
				$_SESSION['logado']['permissoes']['nmcidade'] = array();
				$_SESSION['logado']['permissoes']['cdestado'] = array();

				while ($res = mysql_fetch_assoc($sql)) {
					$_SESSION['logado']['permissoes']['cdcidade'][$x] = $res['cdcidade'];
					$_SESSION['logado']['permissoes']['nmcidade'][$x] = $res['nmcidade'];
					$_SESSION['logado']['permissoes']['cdestado'][$x] = $res['cdestado'];
					$x++;
				}

				$json = array('retorno' => 1);
			}
		}
		//retorno json para o formulário de login
		echo json_encode($json);
	}
}