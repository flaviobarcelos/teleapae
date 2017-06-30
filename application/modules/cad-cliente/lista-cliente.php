<?php
/**
 * Listagem dos clientes cadastrados
 * - Criado em 11/01/2011
 * 
 * @author Niury Martins - http://niurymartins.com.br
 */
include_once(CLASSES . '/class.Paginacao.php');

$array_include_arq = array(
array('type' => 'text/javascript', 'name' => 'funcoes.js'));

require(INCLUDES . '/inc.header.php'); ?>
<div class="nav">
	> <a href="<?php echo montalink('cadastros');?>">Cadastros</a>
	> Cliente
</div>

<fieldset style="width:550px;">
	<legend>Pesquisa</legend>
	<form method="post" action="<?php echo montalink('lista-cliente');?>">
		<p>
			<?php echo label('Termo', 'termo'); ?>
			<input type="text" value="<?php echo $_REQUEST['termo'];?>" style="width:250px;" name="termo" id="termo" maxlength="100" />
			<input type="submit" name="pesquisar" id="pesquisar" value="Pesquisar" class="botao" />
		</p>
	</form>
</fieldset>
<?php 
//operação
echo '<p class="operacao">';
echo '<img src="' , IMG , '/icons/add.png" alt="Cadastrar" title="Cadastrar" />';
echo '<a href="' , montalink('form-cad-cliente') , '&amp;produto">Cadastrar cliente</a>';
echo '</p>';
echo '<div class="clear"></div>';

//caso exista mensagem para exibir
if (isset($_SESSION['msg']['cadcliente'])) {
	echo show_session_msg('cadcliente');
	destroy_session_msg('cadcliente');
}

//caso o usuário tenha clicado em pesquisar
if (isset($_REQUEST['termo'])) {
	$termo = var_insert_db($_REQUEST['termo']);
	if ($termo != '') {
		$where = "and (tb_cliente.nmcliente like '%$termo%' or
				   tb_cliente.email like '%$termo%')";
		$link = '&amp;termo=' . $_REQUEST['termo'];
	}
}

//caso o usuário logado seja franquia
if (somenteFranquia()) {
	$where .= " and tb_cidade.cdcidade in (" . getCidade() . ")";
}
else {
	$where .= '';
}

//seleciona o total de empresas cadastradas
$sql = "select count(tb_cliente.cdcliente) as num
		from tb_cliente
		where tb_cliente.excluido = 'N' $where";
$sql = mysql_query($sql, $con);
$sql = mysql_fetch_assoc($sql);

//Paginação
$paginacao = new Paginacao();
$paginacao->set_num_reg_pag(20);
$paginacao->set_num_reg_tot($sql['num']);
$paginacao->set_site_link(montalink('lista-cliente') . $link);
$pag = $_GET['pag'] ? $_GET['pag'] : 1 ;
$paginacao->set_pagina_atual($pag);

//listagem das empresas
$sql = "select *
		from 
			tb_cliente
		where 
			tb_cliente.excluido = 'N' $where
		order by 
			tb_cliente.nmcliente
		limit
			" . $paginacao->get_reg_ini() . ', ' . $paginacao->get_num_reg_pag();
$sql = mysql_query($sql, $con);

if (!mysql_num_rows($sql)) {
	echo msg('information', 'Nenhum cliente cadastrado');
	require(INCLUDES . '/inc.footer.php');
	exit(1);
}

$paginacao->show();

echo '<table style="width:100%;">';
echo $table = '<tr>
			<th style="width:25%;">Nome</th>
			<th style="width:25%;">e-mail</th>
			<th style="width:25%;">Cidade/ Estado</th>
			<th style="width:5%;">Ativo</th>
			<th colspan="3" style="width:5%;">Ações</th>
			</tr>';
$x = 0;
while ($res = mysql_fetch_assoc($sql)) {
	array_walk($res, 'array_var_show');
	$class = ++$x % 2 == 0 ? 'lista_par' : 'lista_impar' ;
	echo '<tr class="' , $class , '">';
	echo '<td>' , marcar_pesquisa($res['nmcliente'], $_REQUEST['termo']) , '</td>';
	echo '<td>' , marcar_pesquisa($res['email'], $_REQUEST['termo']) , '</td>';
	echo '<td>' , marcar_pesquisa($res['nmcidade'] . '/' . $res['sgestado'], $_REQUEST['termo']) , '</td>';
	echo '<td style="text-align:center;">' , sim_nao($res['ativo']) , '</td>';

	//ver pedidos
	echo '<td style="text-align:center;">';
	echo '<a target="_blank" href="' , montalink('lista-pedido') , '&amp;pesquisar&amp;termo=' , $res['email'] , '">';
	echo '<img src="' , IMG , '/icons/lupa.png" alt="Visualizar pedidos" title="Visualizar pedidos" />';
	echo '</a>';
	echo '</td>';
	
	//editar
	echo '<td style="text-align:center;">';
	echo '<a href="' , montalink('form-cad-cliente') , '&amp;cdcliente=' , $res['cdcliente'] , '">';
	echo '<img src="' , IMG , '/icons/editar.png" alt="editar" title="Editar" />';
	echo '</a>';
	echo '</td>';

	//excluir
	echo '<td style="text-align:center;">';
	echo '<a onclick="return confirma(0);" href="' , montalink('script-delete-cliente') , '&amp;pag=' , $paginacao->get_pagina_atual() , '&amp;cdcliente=' , $res['cdcliente'] , '">';
	echo '<img src="' , IMG , '/icons/delete.png" alt="excluir" title="Excluir" />';
	echo '</a>';
	echo '</td>';
	echo '</tr>';
}

echo $table;
echo '</table>';

$paginacao->show();
require(INCLUDES . '/inc.footer.php');