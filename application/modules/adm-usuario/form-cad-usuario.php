<?php

/**
 * Formulário de cadastro de usuário
 * - Criado em 22/10/2010
 * 
 * @author Niury Martins Pereira
 */

$array_include_arq = array(
array('type' => 'text/javascript', 'name' => 'ajax.js'),
array('type' => 'text/javascript', 'name' => 'funcoes.js')
);

require(INCLUDES . '/inc.header.php');
?>
<div class="nav">
	> <a href="<?php echo montalink('administracao');?>">Administração</a>
	> <a href="<?php echo montalink('lista-usuarios-cadastrados');?>">Administração de usuários</a>
	> Cadastrar usuário
</div>
<?php

//verifica se já existe o cód do usuário
if (isset($_GET['cdusuario'])) {
	$cdusuario = (int)$_GET['cdusuario'];
}
?>

<fieldset style="width:600px;"><legend>Usuário</legend>

	<?php //dados do usuário ?>
	<fieldset style=" width:auto;">
		<legend>Dados</legend>
		<form method="post" action="<?php echo montalink('script-cad-usuario');?>">
				<?php
				echo '<p>';
				//mensagens
				if (isset($_SESSION['msg']['msg-salvar-dados'])) {
					echo show_session_msg('msg-salvar-dados');
					destroy_session_msg('msg-salvar-dados');
				}

				//verifica se existe o post para preencher o formulário
				if (isset($_SESSION['post'])) {
					extract($_SESSION['post']);
					$nmusuario = stripslashes($nmusuario);
					$email = stripslashes($email);
					$login = stripslashes($login);
					unset($_SESSION['post']);
				}
				//seleciona os dados do banco
				else if (isset($cdusuario)) {
					$sql = "select * from sis_usuario where cdusuario = $cdusuario";
					$sql = mysql_query($sql, $con_sma);
					if (mysql_num_rows($sql)) {
						$sql = mysql_fetch_assoc($sql);
						extract($sql);
					}
				}

				//cdusuario
				if ($cdusuario) {
					echo '<input type="hidden" name="cdusuario" value="' , $cdusuario , '" />';
				}
				//Nome do usuário
				echo label('Nome', 'nmusuario', true, 'width:120px;');
				echo '<input type="text" value="' , $nmusuario , '" maxlength="100" name="nmusuario" id="nmusuario" style="width:350px;" />';
				echo '<br />';

				//E-mail do usuário
				echo label('E-mail', 'email', true, 'width:120px;');
				echo '<input type="text" maxlength="100" value="' , $email , '" name="email" id="email" style="width:350px;" />';
				echo '<br />';

				//E-mail do usuário
				echo label('Login', 'login', true, 'width:120px;');
				echo '<input type="text" maxlength="16" value="' , $login , '" name="login" id="login" style="width:150px;" />';
				echo '<span class="dscampo">Deve conter entre 5 e 16 caracteres</span>';
				echo '<br />';
				//alterar senha
				echo label('Permitir alterar senha:', 'alterar_senha', true, 'width:150px;');

				$checked = $alterar_senha != 'N' ? ' checked="checked" ' : '' ;
				echo '<input type="radio" ' , $checked , ' name="alterar_senha" value="S" />Sim';
				$checked = $alterar_senha == 'N' ? ' checked="checked" ' : '' ;
				echo '<input type="radio" ' , $checked , ' name="alterar_senha" value="N" />Não';
				echo '</p>';

				//botao de salvar
				echo '<p style="margin-top:25px;text-align:center">';
				echo '<input type="submit" class="botao" name="salvar_dados" value="Salvar" />';
				echo '</p>';
				?>
		</form>
	</fieldset>
	
	<?php //senha do usuário ?>
	<fieldset style="width:auto;">
		<legend>Senha</legend>
		<?php
		if (!$cdusuario) {
			echo msg('information', 'Antes de atribuir uma senha ao usuário, é necessário que preencha o
					  formulário de dados do usuário e clique em salvar.');
		}
		else {

			echo '<form method="post" action="' , montalink('script-cad-usuario') , '">';

			if (isset($_SESSION['msg']['msg-salvar-senha'])) {
				echo '<p>';
				echo show_session_msg('msg-salvar-senha');
				echo '</p>';
				destroy_session_msg('msg-salvar-senha');
			}

			//caso o usuário ainda não tenha senha o sitema exibe uma mensagem
			echo '<p>';
			$sql_senha = "select senha from sis_usuario where cdusuario = '$cdusuario'";
			$sql_senha = mysql_query($sql_senha, $con_sma);
			$sql_senha = mysql_fetch_assoc($sql_senha);

			if ($sql_senha['senha'] == '') {
				echo '<span style="color:#FF0000;">** O usuário ainda não possui senha</span>';
			}
			else {
				echo '<span style="color:green;">** O usuário já possui senha</span>';
			}
			echo '</p>';

			echo '<p>';
			echo '<span class="dscampo">A senha pode conter no máximo 16 caracteres</span>';
			echo '</p>';

			echo '<p style="margin-top:10px;">';
			echo '<input type="hidden" name="cdusuario" value="' , $cdusuario , '" />';
			//senha
			echo label('Nova senha', 'senha', true, 'width:100px;');
			echo '<input type="password" maxlength="16" name="senha" id="senha" style="width:150px;" />';
			echo '<br />';

			//repita a nova senha
			echo label('Repita a nova senha', 'rsenha', true, 'width:100px;');
			echo '<input type="password" maxlength="16" name="rsenha" id="rsenha" style="width:150px;" />';
			echo '<br />';
			echo '</p>';

			//salvar
			echo '<p style="text-align:center; clear:both; margin-top:25px;">';
			echo '<input type="submit" class="botao" value="Salvar" id="salvar" name="salvar-senha" />';
			echo '</p>';
			echo '</form>';
		}
		?>
	</fieldset>
	
	<?php //tipos de usuário ?>
	<fieldset style="width:auto;">
		<legend>Tipos</legend>
		<?php 

		if (!$cdusuario) {
			echo msg('information', 'Antes de atribuir algum tipo ao usuário, é necessário que preencha o
					  formulário de dados do usuário e clique em salvar.');
		}
		else {
			//formulário
			echo '<form method="post" action="' , montalink('script-cad-usuario') , '">';
			echo '<p>';

			if (isset($_SESSION['msg']['msg-salvar-tpusuario'])) {
				echo show_session_msg('msg-salvar-tpusuario');
				destroy_session_msg('msg-salvar-tpusuario');
			}

			echo '<input type="hidden" id="cdusuario" name="cdusuario" value="' , $cdusuario , '" />';

			if (!in_array('AD', $_SESSION['logado']['usuario']['tpusuario']['sgtpusuario']) and
			in_array('FR', $_SESSION['logado']['usuario']['tpusuario']['sgtpusuario'])) {
				$aux = " and sis_tpusuario.sgtpusuario in ('LO', 'CO') ";
			}
			else {
				$aux = '';
			}

			$sql = "select *
					from sis_tpusuario 
					where sis_tpusuario.cdtpusuario not in (
							select cdtpusuario 
							from sis_usuario_tpusuario 
							where cdusuario = '$cdusuario'
					) $aux
					order by nmtpusuario";
			$sql = mysql_query($sql, $con_sma);

			echo label('Tipos', 'cdtpusuario', true, 'width:60px;');
			echo '<select style="width:270px;" name="cdtpusuario" id="cdtpusuario">';
			echo '<option value="">' , SELECIONE , '</option>';
			while($res = mysql_fetch_assoc($sql)) {

				$class = ++$x % 2 != 0 ? 'lista_par' : 'lista_impar' ;
				echo '<option class="' , $class , '" value="' , $res['cdtpusuario'] , '">' , $res['nmtpusuario'] , '</option>';
			}
			echo '</select>';
			echo '<input type="submit" name="salvar_tpusuario" id="salvar_tpusuario" value="Salvar" class="botao" />';
			echo '</p>';
			echo '</form>';

			//listagem dos tipos já atribuidos
			$sql = "select *
				from sis_tpusuario, sis_usuario_tpusuario
				where sis_usuario_tpusuario.cdusuario = $cdusuario and
					  sis_usuario_tpusuario.cdtpusuario = sis_tpusuario.cdtpusuario
				order by sis_tpusuario.nmtpusuario";
			$sql = mysql_query($sql, $con_sma);
			if (!mysql_num_rows($sql)) {
				echo msg('information', 'O usuário não possui nenhum tipo de usuário vinculado');
			}
			else {
				echo '<table style="width:100%;">';
				echo '<tr>';
				echo '<th style="width:95%;">Tipo</th>';
				echo '<th></th>';
				echo '</tr>';

				$x = 0;
				while ($res = mysql_fetch_array($sql)) {

					$class = ++$x % 2 == 0 ? 'lista_par' : 'lista_impar' ;
					echo '<tr class="' , $class , '">';
					echo '<td>' , $res['nmtpusuario'] , '</td>';

					//excluir tipo
					echo '<td style="text-align:center;">';
					echo '<a onclick="return confirma(0);" href="' , montalink('script-cad-usuario') , '&amp;excluir-tpusuario&amp;cdtpusuario=' , $res['cdtpusuario'] , '&amp;cdusuario=' , $res['cdusuario'] , '">';
					echo '<img src="' , IMG , '/icons/delete.png" alt="Excluir" title="Desvilcular tipo de usuário" />';
					echo '</a>';
					echo '</td>';
					echo '</tr>';
				}
				echo '</table>';
			}
		}
		?>
	</fieldset>
	<fieldset>
		<legend>Empresas de acesso (No caso dos lojistas)</legend>
		<?php 
		if (!isset($cdusuario)) {
			echo msg('information', 'Para atribuir permissão de acesso à uma ou mais empresas é necessário cadastrar os dados e os tipos de usuário');
		}
		else {
			//verifica se o usuário é administrador
			$sql = "select *
					from sis_usuario_tpusuario, sis_tpusuario
					where sis_usuario_tpusuario.cdusuario = '$cdusuario' and
					      sis_usuario_tpusuario.cdtpusuario = sis_tpusuario.cdtpusuario and      
					      sis_tpusuario.sgtpusuario = 'AD'";
			$sql = mysql_query($sql, $con);
			if (mysql_num_rows($sql)) {
				echo msg('ok', 'Usuários do tipo administrador possuem acesso aos dados de todas as empresas e módulos do sistema');
			}
			else {

				//verifica se o usuário é logista
				$sql = "select *
						from sis_usuario_tpusuario, sis_tpusuario
						where sis_usuario_tpusuario.cdusuario = '$cdusuario' and
						      sis_usuario_tpusuario.cdtpusuario = sis_tpusuario.cdtpusuario and      
						      sis_tpusuario.sgtpusuario = 'LO'";
				$sql = mysql_query($sql, $con);
				if (!mysql_num_rows($sql)) {
					echo msg('information', 'Somente aos usuários do tipo lojista devem ser atribuídas permissões de acesso aos dados de determinadas empresas');
				}
				else {
					if (isset($_SESSION['msg']['msg-salvar-empresa'])) {
						echo '<p>';
						echo show_session_msg('msg-salvar-empresa');
						echo '</p>';
						destroy_session_msg('msg-salvar-empresa');
					}

					echo '<form method="post" action="' , montalink('script-cad-usuario') , '">';
					echo '<p>';

					echo '<input type="hidden" name="cdusuario" value="' , $cdusuario , '" />';

					echo label('Empresa', 'cdempresa', true, 'width:60px;');
					echo '<select style="width:277px;" name="cdempresa" id="cdempresa">';
					echo '<option value="0">' , SELECIONE , '</option>';

					if (!in_array('AD', $_SESSION['logado']['usuario']['tpusuario']['sgtpusuario']) and
					in_array('FR', $_SESSION['logado']['usuario']['tpusuario']['sgtpusuario'])) {
						$aux = " and tb_empresa.cdcidade in (select cdcidade from sis_usuario_cidade where cdusuario = '{$_SESSION['logado']['usuario']['cdusuario']}') ";
					}
					else {
						$aux = '';
					}

					$sql = "select *
							from tb_empresa
							where cdempresa not in(
									select cdempresa 
									from sis_usuario_empresa 
									where cdusuario = '$cdusuario') $aux
							order by tb_empresa.nmempresa";
					$sql = mysql_query($sql, $con);
					$x = 0;
					while ($res = mysql_fetch_assoc($sql)) {
						$class = ++$x % 2 != 0 ? 'lista_par' : 'lista_impar' ;
						echo '<option class="' , $class , '" value="' , $res['cdempresa'] , '">' , $res['nmempresa'] , '</option>';
					}
					echo '</select>';
					echo '<input type="submit" name="salvarempresa" id="salvarempresa" value="Salvar" class="botao" />';
					echo '</p>';
					echo '</form>';

					$sql = "select tb_empresa.*
						from tb_empresa, sis_usuario_empresa
						where tb_empresa.cdempresa = sis_usuario_empresa.cdempresa and
							  sis_usuario_empresa.cdusuario = '$cdusuario'
						order by tb_empresa.nmempresa";
					$sql = mysql_query($sql, $con);
					if (mysql_num_rows($sql)) {
						echo '<table style="width:100%;">';
						echo '<tr>';
						echo '<th style="width:95%;">Empresa</th>';
						echo '<th></th>';
						echo '</tr>';
						$x = 0;
						while ($res = mysql_fetch_assoc($sql)) {
							$class = ++$x % 2 == 0 ? 'lista_par' : 'lista_impar' ;
							array_walk($res, 'array_var_show');
							echo '<tr class="' , $class , '">';
							echo '<td>' , $res['nmempresa'] , '</td>';

							//excluir
							echo '<td style="text-align:center;">';
							echo '<a onclick="return confirma(0);" href="' , montalink('script-cad-usuario') , '&amp;excluir-empresa&amp;cdusuario=' , $cdusuario , '&amp;cdempresa=' , $res['cdempresa'] , '">';
							echo '<img src="' , IMG , '/icons/delete.png" title="Excluir" alt="Excluir" />';
							echo '</a>';
							echo '</td>';
							echo '</tr>';
						}
						echo '</table>';
					}
					else {
						echo msg('information', 'Nenhuma empresa adicionada');
					}
				}
			}
		}
		?>
		
	</fieldset>
	
	<fieldset>
		<legend>Cidades de acesso (No caso das franquias)</legend>
		<?php 
		if (!isset($cdusuario)) {
			echo msg('information', 'Para atribuir permissão de acesso à uma ou mais cidades é necessário cadastrar os dados e os tipos de usuário');
		}
		else {
			$sql = "select *
					from sis_usuario_tpusuario, sis_tpusuario
					where sis_usuario_tpusuario.cdusuario = '$cdusuario' and
					      sis_usuario_tpusuario.cdtpusuario = sis_tpusuario.cdtpusuario and      
					      sis_tpusuario.sgtpusuario = 'AD'";
			$sql = mysql_query($sql, $con);
			if (mysql_num_rows($sql)) {
				echo msg('ok', 'Usuários do tipo administrador possuem acesso à todas cidades');
			}
			else {

				//verifica se o é franquia
				$sql = "select *
						from sis_usuario_tpusuario, sis_tpusuario
						where sis_usuario_tpusuario.cdusuario = '$cdusuario' and
						      sis_usuario_tpusuario.cdtpusuario = sis_tpusuario.cdtpusuario and      
						      sis_tpusuario.sgtpusuario in ('FR', 'CO')";
				$sql = mysql_query($sql, $con);
				if (!mysql_num_rows($sql)) {
					echo msg('information', 'Somente aos usuários do tipo franquia devem ser atribuídas permissões de acesso aos dados de determinadas cidades');
				}
				else {
					if (isset($_SESSION['msg']['msg-salvar-cidade'])) {
						echo '<p>';
						echo show_session_msg('msg-salvar-cidade');
						echo '</p>';
						destroy_session_msg('msg-salvar-cidade');
					}

					echo '<form method="post" action="' , montalink('script-cad-usuario') , '">';
					echo '<p>';
					echo '<input type="hidden" name="cdusuario" value="' , $cdusuario , '" />';

					echo label('Estado', 'cdestado', true, 'width:60px;');
					echo '<select name="cdestado" id="cdestado" onchange="carregar_cidades(this.value, \'cdcidade\');" style="width:55px;">';
					echo '<option value="0">--</option>';

					$sql = "select distinct tb_estado.*
							from 
								tb_estado, 
								tb_cidade
							where 
								tb_estado.cdestado = tb_cidade.cdestado $where
							order by tb_estado.sgestado";

					$sql = mysql_query($sql, $con);
					$x = 0;
					while ($res = mysql_fetch_assoc($sql)) {
						$class = ++$x % 2 == 0 ? 'lista_par' : 'lista_impar' ;
						echo '<option class="' , $class , '" value="' , $res['cdestado'] , '">' , $res['sgestado'] , '</option>';
					}
					echo '</select>';

					echo label('Cidade', 'cdcidade', true, 'width:60px; float:none;');
					echo '<select style="width:250px;" name="cdcidade" id="cdcidade">';
					echo '<option value="0">Selecione o estado</option>';
					echo '</select>';
					echo '<input type="submit" name="salvarcidade" id="salvarcidade" value="Salvar" class="botao" />';
					echo '</p>';
					echo '</form>';

					$sql = "select
							tb_estado.sgestado,
							tb_cidade.nmcidade,
							tb_cidade.cdcidade
						from 
							tb_cidade,
							sis_usuario_cidade,
							tb_estado
						where 
							tb_cidade.cdestado = tb_estado.cdestado and
							tb_cidade.cdcidade = sis_usuario_cidade.cdcidade and
							sis_usuario_cidade.cdusuario = '$cdusuario' $where
						order by 
							tb_estado.sgestado,
							tb_cidade.nmcidade";
					$sql = mysql_query($sql, $con);
					if (mysql_num_rows($sql)) {
						echo '<table style="width:100%;">';
						echo '<tr>';
						echo '<th style="width:9%;">Estado</th>';
						echo '<th style="width:80%;">Cidade</th>';
						echo '<th></th>';
						echo '</tr>';
						$x = 0;
						while ($res = mysql_fetch_assoc($sql)) {
							$class = ++$x % 2 == 0 ? 'lista_par' : 'lista_impar' ;
							array_walk($res, 'array_var_show');
							echo '<tr class="' , $class , '">';
							echo '<td>' , $res['sgestado'] , '</td>';
							echo '<td>' , $res['nmcidade'] , '</td>';

							//excluir
							echo '<td style="text-align:center;">';
							echo '<a onclick="return confirma(0);" href="' , montalink('script-cad-usuario') , '&amp;excluir-cidade&amp;cdusuario=' , $cdusuario , '&amp;cdcidade=' , $res['cdcidade'] , '">';
							echo '<img src="' , IMG , '/icons/delete.png" title="Excluir" alt="Excluir" />';
							echo '</a>';
							echo '</td>';
							echo '</tr>';
						}
						echo '</table>';
					}
					else {
						echo msg('information', 'Nenhuma cidade adicionada');
					}
				}
			}
		}
		?>
		
	</fieldset>
</fieldset>

<?php require(INCLUDES . '/inc.footer.php');