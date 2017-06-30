<?php
/**
 * Listagem das regiões de recebimento cadastradas
 */
require(INCLUDES . '/inc.header.php');
?>

<div class="nav">
	> <a href="<?php echo montalink('cadastros'); ?>">Cadastros</a>
	> Regiões de recebimento
</div>

<?php 
//operação
echo '<p class="operacao">';
echo '<img src="' , IMG , '/icons/add.png" alt="Cadastrar" title="Cadastrar" />';
echo '<a href="' , montalink('form-cad-regiao') , '">Cadastrar região</a>';
echo '</p>';
echo '<div class="clear"></div>';
?>

<strong>50</strong> registro(s) encontrado(s)
<table style="width:100%;">
<?php
echo $table = '
		  <tr>
		  		<th style="width:30%;">DESCRIÇÃO</th>
		  		<th>BAIRROS</th>
		  		<th style="width:5%;" colspan="2">AÇÕES</th>
		  </tr>';

for ($x = 0; $x <= 10; $x++) {
	$lista = $x % 2 != 0 ? 'lista_par' : 'lista_impar' ;
	echo '<tr class="' , $lista , '">';
	echo '<td>REGIÃO ' , $x , '</td>';
	echo '<td>';
	echo 'IPATINGA - BELA VISTA<br /> 
		  IPATINGA - BOM RETIRO<br /> 
		  IPATINGA - HORTO';
	echo '</td>';
	
	//editar
	echo '<td style="text-align:center;">';
	echo '<a href="' , montalink('form-cad-regiao') , '&amp;cdregiao=' , $res['cdregiao'] , '">';
	echo '<img src="' , IMG , '/icons/editar.png" alt="editar" title="Editar" />';
	echo '</a>';
	echo '</td>';

	//excluir
	echo '<td style="text-align:center;">';
	echo '<a onclick="return confirma(0);" href="' , montalink('script-delete-regiao') , '&amp;cdregiao=' , $res['cdregiao'] , '">';
	echo '<img src="' , IMG , '/icons/delete.png" alt="excluir" title="Excluir" />';
	echo '</a>';
	echo '</td>';
	
	echo '</tr>';
}

echo $table;
?>
</table>

<?php
require(INCLUDES . '/inc.footer.php');