<?php 
/**
 * P�gina inicial do sistema
 * - Criado em 06/09/2010
 * 
 * @author Niury Martins - http://niurymartins.com.br
 */
require(INCLUDES . '/inc.header.php');
echo '<p style="text-align:center; margin-bottom:40px;">
			<img src="', IMG ,'/information.gif" alt="Dica!" /> Para uma melhor 
			navega��o recomendamos que voc� pressione a tecla F11 do seu teclado, pois 
			dessa forma o navegador ocupar� toda a tela do monitor.
		</p>';

echo monta_menu_tppagina('default');
require(INCLUDES . '/inc.footer.php');
