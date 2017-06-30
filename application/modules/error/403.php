<?php 

/**
 * Página de erro de permissão de acesso
 * 
 * @author Niury Martins - http://niurymartins.com.br
 */

require(INCLUDES . '/inc.header.php'); ?>
<h1>Erro 403</h1>
<div class="error">Ops! Desculpe, você não possui permissões suficientes para acessar a página solicitada</div>
<p style="text-align:center">
	<a href="javascript:history.go(-1)">Voltar para página anterior</a>
</p>
<?php require(INCLUDES . '/inc.footer.php'); ?>