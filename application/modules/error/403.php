<?php 

/**
 * P�gina de erro de permiss�o de acesso
 * 
 * @author Niury Martins - http://niurymartins.com.br
 */

require(INCLUDES . '/inc.header.php'); ?>
<h1>Erro 403</h1>
<div class="error">Ops! Desculpe, voc� n�o possui permiss�es suficientes para acessar a p�gina solicitada</div>
<p style="text-align:center">
	<a href="javascript:history.go(-1)">Voltar para p�gina anterior</a>
</p>
<?php require(INCLUDES . '/inc.footer.php'); ?>