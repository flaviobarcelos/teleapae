<?php

/**
 * Listagem dos usuários cadastrados no sistema
 * - Criado em 22/10/2010
 * 
 * @author Niury Martins Pereira
 */
include_once(CLASSES . '/class.Paginacao.php');
$array_include_arq = array(
array('type' => 'text/javascript', 'name' => 'funcoes.js')
);

require(INCLUDES . '/inc.header.php');
?>
<div class="nav">
	> <a href="<?php echo montalink('administracao');?>">Administração</a>
	> Administração de usuários
</div>

<form method="post" class="form" style="width:580px;" action="<?php echo montalink('lista-usuarios-cadastrados'); ?>">
	<p>
		<?php echo label('Nome', 'nmusuario'); ?>
		<input type="text" name="nmusuario" value="<?php echo $_REQUEST['nmusuario']; ?>" id="nmusuario" style="width:250px;" />
		<input type="submit" name="pesquisar" value="Pesquisar" class="botao" />
	</p>
</form>
<div class="clear"></div>

<?php
$pesquisa = '';
//caso o usuário tenha clicado em pesquisar
if ($_REQUEST['nmusuario']) {
	$nmusuario = addslashes(trim($_REQUEST['nmusuario']));
	$pesquisa = " and sis_usuario.nmusuario like '%$nmusuario%' ";
	$link = '&amp;nmusuario=' . $_REQUEST['nmusuario'];
}

//caso o usuário logado seja franquia, então lista somente os usuários que ele cadastrou e usuários logistas da cidade dele
if (in_array('FR', $_SESSION['logado']['usuario']['tpusuario']['sgtpusuario'])) {
	$aux = " and (
					sis_usuario.cdusuario in (
						  select 
						  	sis_usuario.cdusuario
						  from 
						  	sis_usuario 
						  where 
						  	sis_usuario.cdusuariocad = '{$_SESSION['logado']['usuario']['cdusuario']}'
				    )
				    or
				    sis_usuario.cdusuario in (
				    	 select 
						  	sis_usuario.cdusuario
						  from 
						  	sis_usuario, 
						  	sis_usuario_empresa,
						  	tb_empresa
						  where 
						  	sis_usuario.cdusuario = sis_usuario_empresa.cdusuario and 
						  	sis_usuario_empresa.cdempresa = tb_empresa.cdempresa and
						  	tb_empresa.cdcidade in (" . getCidade() . ")
				    )
			)";
}

//seleciona o total de usuários
$sql = "select count(distinct sis_usuario.cdusuario) as tot
		from sis_usuario, sis_usuario_tpusuario
		where sis_usuario.cdusuario = sis_usuario_tpusuario.cdusuario $pesquisa $aux";
$sql = mysql_query($sql, $con);
$sql = mysql_fetch_assoc($sql);

//Paginação
$paginacao = new Paginacao();
$paginacao->set_num_reg_pag(20);
$paginacao->set_num_reg_tot($sql['tot']);
$paginacao->set_site_link(montalink('lista-usuarios-cadastrados') . $link);
$pag = $_GET['pag'] ? $_GET['pag'] : 1 ;
$paginacao->set_pagina_atual($pag);

$sql = "select distinct sis_usuario.*
		from sis_usuario		
		where sis_usuario.cdusuario $pesquisa $aux
		order by sis_usuario.nmusuario 
		limit
			" . $paginacao->get_reg_ini() . ", " . $paginacao->get_num_reg_pag();
$sql = mysql_query($sql, $con_sma);

//opções
echo '<div class="operacao">';
echo '<a href="' , montalink('form-cad-usuario') , '">';
echo '<img src="' , IMG , '/icons/add.png" alt="Add user" title="Adicionar usuário" />';
echo 'Cadastrar usuário';
echo '</a>';
echo '</div>';
echo '<div class="clear"></div>';

//mensagens
if (isset($_SESSION['msg']['msg-excluir-usuario'])) {
	echo show_session_msg('msg-excluir-usuario');
	destroy_session_msg('msg-excluir-usuario');
}

$paginacao->show();
//total de registros
echo '<span class="dscampo">' , mysql_num_rows($sql) , ' registro(s) encontrado(s)</span>';

//tabela de usuários
echo '<table style="width:100%; margin-top:0;">';
$tabela = '<tr>';
$tabela .= '<th style="width:30%;">Nome</th>';
$tabela .= '<th style="width:20%;">Tipo(s) de usuário</th>';
$tabela .= '<th style="width:20%;">E-mail</th>';
$tabela .= '<th style="width:15%;">Login</th>';
$tabela .= '<th style="width:5%;">Ativo</th>';
$tabela .= '<th style="width:5%;">Alterar senha</th>';
$tabela .= '<th colspan="2">Ações</th>';
$tabela .= '</tr>';
echo $tabela;

$x = 0;
while ($res = mysql_fetch_assoc($sql)) {

	array_walk($res, 'array_var_show');

	//cor da linha
	$class = ++$x % 2 == 0 ? 'lista_par' : 'lista_impar' ;

	//se tiver inativo então coloca a cor da letra mais clara
	$style = $res['ativo'] == 'S' ? '' : 'style="color:#999999;"' ;

	//seleciona os tipos de usuário
	$sql_tpusuario = "select sis_tpusuario.*
					  from sis_tpusuario, sis_usuario_tpusuario
					  where sis_usuario_tpusuario.cdusuario = $res[cdusuario] and
					  	    sis_usuario_tpusuario.cdtpusuario = sis_tpusuario.cdtpusuario
					  order by sis_tpusuario.nmtpusuario";
	$sql_tpusuario = mysql_query($sql_tpusuario, $con_sma);
	$num_rowspan = mysql_num_rows($sql_tpusuario);
	$str_rowspan = mysql_num_rows($sql_tpusuario) > 0 ?  ' rowspan="' . mysql_num_rows($sql_tpusuario) . '" ' : '' ;

	//monta a linha
	echo '<tr class="' , $class , '" ' , $style , '>';
	echo '<td ' , $str_rowspan , '>' , $res['nmusuario'] , '</td>';
	$res_tpusuario = mysql_fetch_assoc($sql_tpusuario);
	echo '<td>' , $res_tpusuario['nmtpusuario'] , '</td>';
	echo '<td ' , $str_rowspan , '>' , $res['email'] , '</td>';
	echo '<td ' , $str_rowspan , '>' , $res['login'] , '</td>';
	echo '<td ' , $str_rowspan , ' style="text-align:center;">' , sim_nao($res['ativo']) , '</td>';
	echo '<td ' , $str_rowspan , ' style="text-align:center;">' , sim_nao($res['alterar_senha']) , '</td>';

	//editar
	echo '<td ' , $str_rowspan , ' style="text-align:center;">';
	echo '<a href="' , montalink('form-cad-usuario') , '&amp;cdusuario=' , $res['cdusuario'] , '">';
	echo '<img title="Editar dados" src="' , IMG , '/icons/editar.png" alt="Editar dados" />';
	echo '</a>';
	echo '</td>';

	//excluir
	echo '<td ' , $str_rowspan , ' style="text-align:center;">';
	echo '<a onclick="return confirma(0);" href="' , montalink('script-cad-usuario') , '&amp;excluir-usuario&amp;cdusuario=' , $res['cdusuario'] , '">';
	echo '<img title="Excluir" src="' , IMG , '/icons/delete.png" alt="Excluir" />';
	echo '</a>';
	echo '</td>';
	echo '</tr>';

	//exibe o restante dos tipos de usuário
	if ($num_rowspan > 1) {
		while ($res_tpusuario = mysql_fetch_assoc($sql_tpusuario)) {
			echo '<tr class="' , $class , '">';
			echo '<td>' , $res_tpusuario['nmtpusuario'] , '</td>';
			echo '</tr>';
		}
	}
}

echo $tabela;
echo '</table>';
$paginacao->show();

require(INCLUDES . '/inc.footer.php');