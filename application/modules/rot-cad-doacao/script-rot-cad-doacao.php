<?php
/**
 * Script de inser��o de doa��es
 */
require(INCLUDES . '/inc.header.php');
?>

<div class="nav">
	> <a href="<?php echo montalink('cadastros') ?>">Cadastros</a>
	> <a href="<?php echo montalink('rot-cad-doacao') ?>">Rotina de cadastro de doa��es</a>
	> Execu��o
</div>

<?php

extract($_POST);

if ($mes >= 1 and $mes <= 12 and $ano >= 2013) {
	include_once(CLASSES . '/class.ExecutaRotinaDoacao.php');

	$Rotina = new RotinaDoacao($con, $mes, $ano);
	$x = $Rotina->ExecutaRotina();

	echo msg('ok', 'Foram inseridos ' . $x . ' recibos com sucesso');
}
else {
	echo msg('alert', 'O m�s e/ou ano s�o inv�lidos');
}

echo '<p style="text-align:center; margin-top:40px;"><a href="' , montalink('rot-cad-doacao') , '">Voltar</a></p>';

require(INCLUDES . '/inc.footer.php');