<?php 
/**
 * Página de erro 404
 * 
 * @author Niury Martins - http://niurymartins.com.br
 */

require(INCLUDES . '/inc.header.php'); ?>
<h1>Erro 404</h1>
<div class="error">Ops! Desculpe, a página solicitada não foi encontrada</div>
<p style="text-align:center">
	<a href="javascript:history.go(-1)">Voltar para página anterior</a>
</p>
<?php require(INCLUDES . '/inc.footer.php'); ?>