<?php
/**
 * Arquivo que possui as fun��es globais do sistema
 * 
 * @author Niury Martins - http://niurymartins.com.br
 */

/**
 * tratamento de SQL Injection
 *
 * @param string $var
 * @return string
 */
function var_insert_db($var) {
	return addslashes(html_entity_decode(trim(strtoupper($var))));
}

/**
 * tratamento de scipt injection
 *
 * @param string $var
 * @return string
 */
function var_show($var) {
	return htmlentities(stripslashes($var));
}

/**
 * percorre o array aplicando a fun��o var_insert_db
 *
 * @param array $var
 */
function array_insert_db(&$var) {
	$var = var_insert_db($var);
}

/**
 * percorre o array aplicando a fun��o var_show
 *
 * @param array $var
 */
function array_var_show(&$var) {
	$var = var_show($var);
}

/**
 * retorna o nome do arquivo a ser carregado para o usu�rio
 *
 * @return string
 */
function getpage() {

	$_SERVER['QUERY_STRING'] = trim($_SERVER['QUERY_STRING']);
	$var = explode('&', $_SERVER['QUERY_STRING']);

	if (count($var)) {
		//verifica se o arquivo e o m�dulo existe
		if ($var[0] != '' and $var[1] != '') {

			//$var[1] = $var[0] . '.' . $var[1] . '.php';
			$var[1] = $var[1] . '.php';
			return MODULES . '/' . $var[0] . '/' . $var[1];
		}
	}
	return MODULES . '/default/default-home.php';

}

/**
 * fun��o respons�vel pela montagem do link entre os arquivos
 *
 * @param string $nmarquivo
 * @param string $separador
 * @return string
 */
function montalink($nmarquivo = '', $separador = '&amp;') {
	global $con;

	//retira ".php" do nome do arquivo caso tenha
	if (strstr($nmarquivo, '.php')) {
		$nmarquivo = substr($nmarquivo, 0, strlen($nmarquivo) - 4);
	}

	$nmarquivo = strtolower($nmarquivo);

	$link = '';
	for ($x = 0; $x < count($_SESSION['logado']['permissoes']['nmarquivo']); $x++) {

		$arq = $_SESSION['logado']['permissoes']['nmarquivo'][$x];
		$arq = substr($arq, 0, strlen($arq) - 4);

		if ($nmarquivo == $arq) {

			$link = '?' . $_SESSION['logado']['permissoes']['sgmodulo'][$x] . $separador . $arq;
		}
	}

	if (!$link) {
		$sql = "select distinct sis_modulo.sgmodulo
				from 
					sis_pagina, 
					sis_modulo, 
					sis_modulo_pagina
				where 
					sis_pagina.cdpagina = sis_modulo_pagina.cdpagina and
					sis_modulo_pagina.cdmodulo = sis_modulo.cdmodulo and
					sis_pagina.nmarquivo = '$nmarquivo.php'";
		$sql = mysql_query($sql, $con);
		if (mysql_num_rows($sql)) {
			$sql = mysql_fetch_assoc($sql);
			$link = '?' . $sql['sgmodulo'] . $separador . $nmarquivo;
		}
	}

	return $link;
}

/**
 * respons�vel por montar o menu principal de acordo com o tipo de p�gina
 *
 * @param string $nmtppagina
 * @return string
 */
function monta_menu_tppagina($nmtppagina) {

	$nmtppagina = trim(strtoupper($nmtppagina));
	$conteudo = '';
	$return  = '';
	$tr = 0;

	for ($x = 0; $x < count($_SESSION['logado']['permissoes']['nmarquivo']); $x++) {

		$tppagina = $_SESSION['logado']['permissoes']['nmtppagina'][$x];
		if ($tppagina == $nmtppagina and $_SESSION['logado']['permissoes']['inicial'][$x] == 'S') {

			if ($tr == 0) {
				$conteudo .= '<tr>';
			}

			$conteudo .= '<td>';
			$conteudo .= '<a href="' . montalink($_SESSION['logado']['permissoes']['nmarquivo'][$x]) . '">';
			$conteudo .= '<img src="' . IMG . '/' . $_SESSION['logado']['permissoes']['imgmodulo'][$x] . '" alt="' . $_SESSION['logado']['permissoes']['nmmodulo'][$x] . '" title="' . $_SESSION['logado']['permissoes']['dsmodulo'][$x] . '" />';
			$conteudo .= '<br />';
			$conteudo .= $_SESSION['logado']['permissoes']['nmmodulo'][$x];
			$conteudo .= '</a></td>';

			if ($tr == 4) {
				$conteudo .= '</tr>';
				$tr = 0;
			}
			else {
				$tr++;
			}
		}
	}

	if ($conteudo != '') {
		$return .= '<div class="menu_centralizado">';
		$return .= '<table>';
		$return .= $conteudo;
		$return .= '</tr>';
		$return .= '</table>';
		$return .= '</div>';
	}

	return $return;
}

/**
 * valida se o usu�rio est� logado
 *
 * @return boolean
 */
function isauth() {
	return (isset($_SESSION['logado']) and
	$_SESSION['logado']['usuario']['cdusuario'] != '');
}

/**
 * verifica se o usu�rio possui permiss�o de acesso � p�gina
 *
 * @param string $page
 * @return boolean
 */
function checkpermission($page) {

	//mostra_array($_SESSION['logado']['permissoes']);
	if (!isauth()) {
		return false;
	}
	else {

		$var = explode('/', $page);
		if (count($var) < 3) {
			return false;
		}
		else {
			for ($x = 0; $x < count($_SESSION['logado']['permissoes']['nmarquivo']); $x++) {
				if ($_SESSION['logado']['permissoes']['nmarquivo'][$x] == $var[4] and
				$_SESSION['logado']['permissoes']['dirmodulo'][$x] == $var[3]) {
					return true;
				}
			}
			return false;
		}
	}
}

/**
 * conver a string para o tipo de login utilizado no sistema
 *
 * @param string $login
 * @return string
 */
function char_login($login) {
	return strtoupper($login);
}

/**
 * converte a string para o tipo de senha utilizada no sistema
 *
 * @param string $senha
 * @return string
 */
function char_senha($senha) {
	return md5(trim(strtoupper($senha)));
}

/**
 * Exibe o conte�do de um array na tela
 *
 * @param array $array
 */
function mostra_array($array) {
	echo '<pre>';
	print_r($array);
	echo '</pre>';
}

/**
 * fun��o para armazenar mensagens em uma vari�vel
 *
 * @param string $msg
 * @param string $txt
 */
function set_msg(&$msg, $txt) {
	if($msg != '') {
		$msg .= '<br />';
	}

	$msg .= '- ' . $txt;
}

/**
 * retorna a mensagem de erro no formato html
 *
 * @param string $type
 * @param string $txt
 * @param boolean $refresh
 * @param string $url
 * @param int $time
 * @param boolean $log
 * @return string of html code
 */
function msg($type, $txt, $refresh = false, $url = '', $time = 0, $log = false)
{
	$var = '<div class="' . $type . '">' . $txt . '</div>';
	if ($refresh) {
		$var .= '<meta http-equiv="refresh" content="' . $time . '; URL=' . $url . '" />';
	}
	return $var;
}

/**
 * armazena mensagens em uma sess�o
 *
 * @param string $posmsg
 * @param string $tpmsg
 * @param string $msg
 */
function set_session_msg($posmsg, $tpmsg, $msg) {
	$_SESSION['msg'] = array();
	$_SESSION['msg'][$posmsg] = array();
	$_SESSION['msg'][$posmsg]['type'] = $tpmsg;
	$_SESSION['msg'][$posmsg]['msg'] = $msg;
}

/**
 * exibe o erro que est� armazenado em uma sess�o
 *
 * @param string $posmsg
 * @return string of html code
 */
function show_session_msg($posmsg) {
	return msg($_SESSION['msg'][$posmsg]['type'], $_SESSION['msg'][$posmsg]['msg']);
}

/**
 * Destroi a posi��o informada na sess�o de mensagem
 *
 * @param string $posmsg
 */
function destroy_session_msg($posmsg) {
	unset($_SESSION['msg'][$posmsg]);
}

/**
 * prepara um valor num�rico para ser inserido no mysql
 *
 * @param real $valor
 * @return real
 */
function number_insert_mysql($valor) {

	if (strpos($valor, ',')) {
		$valor = str_replace('.', '', $valor);
		$valor = str_replace(',', '.', $valor);
	}
	return $valor;
}

/**
 * prepara um valor num�rico para ser exibido na tela 
 *
 * @param real $valor
 * @return string
 */
function number_show($valor) {
	$valor = $valor > 0 ? $valor : 0.00 ;
	return number_format($valor, 2, ',', '.');
}

/**
 * Finaliza o carregamento da p�gina e exibe uma mensagem
 *
 * @param string $msg_type
 * @param string $msg
 */
function finish_load($msg_type, $msg) {

	echo msg($msg_type, $msg);
	require(INCLUDES . '/inc.footer.php');
	exit(1);
}

/**
 * converte um valor real para um valor inteiro
 *
 * @param real $num
 * @return string
 */
function decimaltoperc($num) {
	return str_replace('.' , ',', ($num * 100));
}

/**
 * converte um valor inteiro para real (%)
 *
 * @param real $num
 * @return string
 */
function perctodecimal($num) {

	$num = str_replace(',', '.', $num);
	return number_format(($num / 100), 3, '.', '');
	return 0;
}

/**
 * Escreve um label na tela
 *
 * @param string $txt
 * @param string $for
 * @param boolean $obrig
 * @param string $style
 * @return string
 */
function label($txt, $for='', $obrig = false, $style='') {
	$for = ($for)? 'for="'.$for.'"' : '' ;
	$obrig = ($obrig)? '<span class="obrig">*</span>' : '' ;
	return '<label style="'.$style.'">'. htmlentities(strtoupper($txt), ENT_QUOTES, "ISO-8859-1") . $obrig . ':</label>';
}

/**
 * respons�vel por incluir arquivos CSS e JS na tag header
 *
 * @param array $array
 * @return string of html code
 */
function include_arq($array) {

	$html = '';
	foreach ($array as $arq) {

		if ($arq['type'] == 'text/javascript') {
			$html .= '<script type="text/javascript" ' . $arq['attr'] . ' src="' . JS . '/' . $arq['name'] . '"></script>';
		}
		else if ($arq['type'] == 'text/css') {
			$html .= '<link href="' . CSS . '/' . $arq['name'] . '" rel="stylesheet" type="text/css" />';
		}
	}
	return $html;
}

/**
 * retorna os m�dulos que o usu�rio possui acesso
 *
 * @return array
 */
function getModulos($nmtppagina = '') {

	$array = array();
	foreach ($_SESSION['logado']['permissoes'] as $var) {
		if (!in_array($var['nmmodulo'], $array)) {
			$array[] = $var['nmmodulo'];
		}
	}
	return $array;
}

/**
 * Retorna os tipos de p�gina que o usu�rio possui acesso
 *
 * @return array
 */
function getTppaginas() {

	return $_SESSION['logado']['permissoes']['nmtppagina'];
}

/**
 * inverte o formato da data
 *
 * @param string $data
 * @param char $separador
 * @return string
 */
function inverte_formato_data($data, $separador = '-') {

	if(strpos($data, '-')) {
		$data = explode('-', $data);
	}
	else if(strpos($data, '/')) {
		$data = explode('/', $data);
	}
	else {
		return false;
	}

	if(count($data)!=3) {
		return false;
	}

	return $data[2].$separador.$data[1].$separador.$data[0];
}

/**
 * Retorna a string de acordo com o caractere passado como par�metro
 *
 * @param string $char
 * @return string
 */
function sim_nao($char) {
	return strtoupper($char) == 'S' ? 'SIM' : 'N�O' ;
}

/**
 * Marca o texto com o termo de pesquisa
 *
 * @param string $palavra
 * @param string $termo_pesquisa
 * @return string
 */
function marcar_pesquisa($palavra = '', $termo_pesquisa = '') {

	$p1 = strtoupper(trim($palavra));
	$p2 = strtoupper(trim($termo_pesquisa));

	$termo_pesquisa = trim($termo_pesquisa);
	$palavra = trim($palavra);

	if ($p1 and $p2) {
		$pos = strpos($p1, $p2);
		if ($pos !== false) {
			$pos += (strlen($termo_pesquisa));
			$p2 = substr($palavra, $pos - strlen($p2), $pos);
			$palavra = str_replace($p2, '<span style="background:yellow;">' . $p2.'</span>', $palavra);
		}
	}
	return $palavra;
}

/**
 * calcula o somat�rio de dias, meses ou anos em uma data
 *
 * @param date $data
 * @param string $formato_data
 * @param int $dias
 * @param int $meses
 * @param int $ano
 * @return date
 */
function somar_data($data, $formato_data, $dias, $meses, $ano)
{
	$formato = false;
	$separador = '-';

	//encontra o separador da data
	if (strpos($data, "-")) {
		$separador = '-';
	}
	else if (strpos($data, "/")) {
		$separador = '/';
	}

	//inverte a data caso necess�rio
	if ($formato_data == 'Y-m-d' or $formato_data == 'Y/m/d') {
		$data = inverte_formato_data($data, $separador);
		$formato = true;
	}

	//separa a data e faz o somat�rio
	$data = explode($separador, $data);
	$newData = date("d/m/Y", mktime(0, 0, 0, $data[1] + $meses, $data[0] + $dias, $data[2] + $ano));

	if ($formato) {
		$newData = inverte_formato_data($newData, $separador);
	}

	return $newData;
}

/**
 * inverte uma data que s� possui m�s e ano
 *
 * @param date $data
 * @param string $separador
 * @return string
 */
function inverte_formato_data_mes_ano($data, $separador = '-') {
	if(strpos($data, '-')) {
		$data = explode('-', $data);
	}
	else if(strpos($data, '/')) {
		$data = explode('/', $data);
	}
	else {
		return false;
	}

	if(count($data)!=2) {
		return false;
	}

	return $data[1].$separador.$data[0];
}

/**
 * Valida se o CPF � v�lido
 *
 * @param string $cpf
 * @return boolean
 */
function valida_cpf($cpf) {
	$cpf = str_pad(ereg_replace('[^0-9]', '', $cpf), 11, '0', STR_PAD_LEFT);

	// Verifica se nenhuma das sequ�ncias abaixo foi digitada, caso seja, retorna falso
	if (strlen($cpf) != 11 || $cpf == '00000000000' || $cpf == '11111111111' || $cpf == '22222222222' || $cpf == '33333333333' || $cpf == '44444444444' || $cpf == '55555555555' || $cpf == '66666666666' || $cpf == '77777777777' || $cpf == '88888888888' || $cpf == '99999999999')
	{
		return false;
	}
	else
	{   // Calcula os n�meros para verificar se o CPF � verdadeiro
		for ($t = 9; $t < 11; $t++) {
			for ($d = 0, $c = 0; $c < $t; $c++) {
				$d += $cpf{$c} * (($t + 1) - $c);
			}

			$d = ((10 * $d) % 11) % 10;

			if ($cpf{$c} != $d) {
				return false;
			}
		}

		return true;
	}
}

/**
 * Validar data. Passar data no formato ingl�s ('2009/01/01')
 *
 * @param date $data
 * @return boolean
 * @example valida_data('2009/01/01')
 */
function valida_data($data) {
	if(strpos($data, '-'))
	{
		$data = explode('-', $data);
	}
	else if(strpos($data, '/'))
	{
		$data = explode('/', $data);
	}
	else return false;
	if(count($data)!=3) return false;
	return @checkdate($data[1], $data[2], $data[0]);
}

/**
 * Retorna o tipo de valor 
 *
 * @param string $sg
 * @return string
 */
function getTpvalor($sg) {
	return (trim($sg) == 'P') ? 'Proventos' : 'Descontos' ;
}

/**
 * Fun��o para validar hora
 *
 * @param time $time
 * @return boolean
 */
function valida_hora($time) {

	$time = explode(':', $time);
	if (count($time) < 3) {
		return false;
	}

	$hour = $time[0];
	$minute = $time[1];
	$second = $time[2];

	if ($hour > -1 && $hour < 24 && $minute > -1 && $minute < 60 && $second > -1 && $second < 60) {
		return true;
	}

	return false;
}

/**
 * Retorna o c�digo das empresas que o usu�rio possui permiss�o de acesso
 * 
 * @param int $cdusuario
 * @return string
 */
function getCdempresas($cdusuario = 0) {
	global $con;
	$cdempresa = 0;

	//caso ele seja administrador ou atendimento ent�o retorna todos as lojas
	$sql = "select DISTINCT sis_tpusuario.sgtpusuario
			from sis_tpusuario, sis_usuario, sis_usuario_tpusuario
			where sis_usuario.cdusuario = '$cdusuario' and
			      sis_usuario.cdusuario = sis_usuario_tpusuario.cdusuario and      
			      sis_usuario_tpusuario.cdtpusuario = sis_tpusuario.cdtpusuario and      
			      sis_tpusuario.sgtpusuario in ('AD', 'AT')";
	$sql = mysql_query($sql, $con);
	if (mysql_num_rows($sql)) {
		$sql = "select cdempresa from tb_empresa";
		$sql = mysql_query($sql, $con);
		while ($res = mysql_fetch_assoc($sql)) {
			$cdempresa .= ',' . $res['cdempresa'];
		}
	}
	else {
		$sql = "select distinct sis_usuario_empresa.cdempresa
				from sis_usuario_empresa
				where sis_usuario_empresa.cdusuario = '$cdusuario'";
		$sql = mysql_query($sql, $con);
		while ($res = mysql_fetch_assoc($sql)) {
			$cdempresa .= ',' . $res['cdempresa'];
		}
	}

	return $cdempresa;
}

/**
 * Faz o cadastramento da altera��o do status de um pedido
 *
 * @param int $cdusuario
 * @param int $cdpedido
 * @param int $cdsitpedido
 * @return boolean
 */
function HistPedido($cdusuario, $cdpedido, $cdsitpedido) {
	global $con;
	$sql = "insert into tb_histpedido
			(cdusuario, cdpedido, cdsitpedido)
			values
			('$cdusuario', '$cdpedido', '$cdsitpedido')";
	return mysql_query($sql, $con);
}

/**
 * Verifica se o funcion�rio � somente do tipo representante
 * 
 * @return boolean
 */
function somenteRep() {
	if(!in_array('AD', $_SESSION['logado']['usuario']['tpusuario']['sgtpusuario']) and
	(in_array('RE', $_SESSION['logado']['usuario']['tpusuario']['sgtpusuario']) or
	in_array('CO', $_SESSION['logado']['usuario']['tpusuario']['sgtpusuario']))) {
		return true;
	}

	return false;
}

/**
 * Verifica se o funcion�rio � somente do tipo atendimento
 * 
 * @return boolean
 */
function somenteAtendimento() {
	if(!in_array('AD', $_SESSION['logado']['usuario']['tpusuario']['sgtpusuario']) and
	in_array('AT', $_SESSION['logado']['usuario']['tpusuario']['sgtpusuario'])) {
		return true;
	}

	return false;
}

/**
 * Verifica se o funcion�rio � somente do tipo lojista
 * 
 * @return boolean
 */
function somenteLojista() {
	if(!in_array('AD', $_SESSION['logado']['usuario']['tpusuario']['sgtpusuario']) and
	in_array('LO', $_SESSION['logado']['usuario']['tpusuario']['sgtpusuario'])) {
		return true;
	}

	return false;
}


/**
 * Retorna as cidades que um usu�rio do tipo franquia possui acesso
 * 
 * @return string $cdcidade
 */
function getCidade() {
	global $con;

	$cdcidade = 0;
	for ($x = 0; $x < count($_SESSION['logado']['permissoes']['cdcidade']); $x++) {
		$cdcidade .= ',' . $_SESSION['logado']['permissoes']['cdcidade'][$x];
	}

	return $cdcidade;
}

/**
 * Valida��o de e-mail
 *
 * @param string $email
 * @return boolean
 */
function valida_email($email) {
	if(eregi("^[a-z0-9_\.\-]+@[a-z0-9_\.\-]*[a-z0-9_\-]+\.[a-z]{2,4}$", "$email")) {
		return true;
	}
	else {
		return false;
	}
}

/**
 * Func��o para validar CNPJ
 *
 * @param string $cnpj
 * @return boolean
 */
function validaCnpj($cnpj) {
	$cnpj = preg_replace ("@[./-]@", "", $cnpj);

	if (strlen ($cnpj) <> 14 or !is_numeric ($cnpj)) {
		return false;
	}

	$j = 5;
	$k = 6;
	$soma1 = "";
	$soma2 = "";

	for ($i = 0; $i < 13; $i++) {
		$j = $j == 1 ? 9 : $j;
		$k = $k == 1 ? 9 : $k;
		$soma2 += ($cnpj{$i} * $k);

		if ($i < 12) {
			$soma1 += ($cnpj{$i} * $j);
		}

		$k--;
		$j--;
	}

	$digito1 = $soma1 % 11 < 2 ? 0 : 11 - $soma1 % 11;
	$digito2 = $soma2 % 11 < 2 ? 0 : 11 - $soma2 % 11;
	return (($cnpj{12} == $digito1) and ($cnpj{13} == $digito2));
}

/**
 * Calcula a idade
 *
 * @param int $dia
 * @param int $mes
 * @param int $ano
 * @return unknown
 */
function calcIdade($dia, $mes, $ano) {
	if (!checkdate($mes, $dia, $ano)) {
		return false;
	}

	$dia_atual = date("d");
	$mes_atual = date("m");
	$ano_atual = date("Y");
	$idade = $ano_atual - $ano;
	if ($mes > $mes_atual) {
		$idade--;
	}

	if ($mes == $mes_atual and $dia_atual < $dia) {
		$idade--;
	}
	return $idade;
}

/**
 * Criar um diret�rio
 *
 * @param string $dir
 * @return boolean
 */
function cria_dir($dir) {
	if (!is_dir($dir)) {
		mkdir($dir);
		chmod($dir, 777);
		return true;
	}

	return false;
}

/**
 * Remove acentos e caracteres especiais
 *
 * @param string $palavra
 * @return string
 */
function removeAcentos($palavra) {
	$palavra = ereg_replace("[^a-zA-Z0-9/-]", "", strtr($palavra, "�������������������������� ", "aaaaeeiooouucAAAAEEIOOOUUC-"));
	return str_replace('__', '_', $palavra);
}

/**
 * Limita os caracteres de uma string
 *
 * @param string $texto
 * @param unt $tam_max
 * @return string
 */
function limitaTexto($texto = '', $tam_max = 50) {
	$retcencias = (strlen($texto) > $tam_max) ? '...' : '';
	$texto = substr($texto, 0, $tam_max);
	return $texto.=$retcencias;
}

/**
 * Monta a url amig�vel de um an�ncio
 *
 * @param string $texto
 * @param int $cdanuncio
 * @return string
 */
function montaUrl($texto) {
	return str_replace('...', '', limitaTexto(strtolower(removeAcentos(html_entity_decode($texto))), 80));
}

/**
 * Monta a barra de navega��o
 *
 * @param class $paginacao
 * @return string
 */
function exibeNavreg($paginacao) {
	$return = '';
	$return .= '<div style="float:left; font-style:italic;">';
	$return .= '<strong style="font-style:italic;">' . ($paginacao->get_reg_ini() + 1) . '</strong> - <strong style="font-style:italic;">' . ($paginacao->get_num_reg_pag() + $paginacao->get_reg_ini()) . '</strong> de <strong style="font-style:italic;">' . $paginacao->get_num_reg_tot() . '</strong>';
	$return .= '</div>';

	return $return;
}

/**
 * Replica os dados tabela de doador para doa��es
 *
 * @param int $cddoador
 * @return void()
 */
function ReplicaDadosDoador($cddoador) {
	global $con;
	$sql = "select
				tb_doador.nmresponsavel,
				tb_doador.nmfantasia,
				tb_doador.cdusuario,
				tb_tpdoador.sgtpdoador
			from
				tb_doador 
				inner join tb_tpdoador on tb_doador.cdtpdoador = tb_tpdoador.cdtpdoador
			where
				tb_doador.cddoador = '$cddoador'";
	$sql = mysql_query($sql, $con);
	if (mysql_num_rows($sql)) {
		$res = mysql_fetch_assoc($sql);

		$sql = "update tb_doacao set
					tb_doacao.nmresponsavel = '$res[nmresponsavel]',
					tb_doacao.nmfantasia = '$res[nmfantasia]',
					tb_doacao.cdusuario = '$res[cdusuario]',
					tb_doacao.sgtpdoador = '$res[sgtpdoador]'
				where
					tb_doacao.cddoador = '$cddoador' and
					tb_doacao.excluido = 'N'";
		mysql_query($sql, $con);
	}
}

/**
 * Atualiza os dados de doa��o e data de contato na tabela de doadores
 *
 * @param int $cddoador
 * @return void()
 */
function ReplicaDadosDoacoes($cddoador = 0, $cddoacao = 0) {
	global $con;

	//seleciona o cod do doador caso n�o exista
	if (!$cddoador and $cddoacao) {
		$sql = "select cddoador from tb_doacao where cddoacao = '$cddoacao'";
		$sql = mysql_query($sql, $con);
		if (mysql_num_rows($sql)) {
			$res = mysql_fetch_assoc($sql);
			$cddoador = $res['cddoador'];
		}
	}

	//atualiza a tabela
	$sql = "select
				sum(tb_doacao.vldoacao) as totdoacao,
				max(tb_doacao.dtcontato) as ultdtcontato
			from
				tb_doacao
			where
				tb_doacao.cddoador = '$cddoador' and
				tb_doacao.excluido = 'N' and
				tb_doacao.cancelado = 'N'";
	$sql = mysql_query($sql, $con);
	if (mysql_num_rows($sql)) {
		$res = mysql_fetch_assoc($sql);

		$sql = "update tb_doador set
					vltotdoacao = '$res[totdoacao]',
					ultdtcontato = '$res[ultdtcontato]'
				where 
					cddoador = '$cddoador'";
		mysql_query($sql, $con);
	}
}

/**
 * Retorna o nome do m�s em portugu�s
 *
 * @param int $mes
 * @return string
 */
function getMesPtbr($mes) {
	
	$mes = (int)$mes;
	$mes_extenso = array(
	1 => 'Janeiro',
	2 => 'Fevereiro',
	3 => 'Mar�o',
	4 => 'Abril',
	5 => 'Maio',
	6 => 'Junho',
	7 => 'Julho',
	8 => 'Agosto',
	9 => 'Setembro',
	10 => 'Outubro',
	11 => 'Novembro',
	12 => 'Dezembro'
	);
	
	return $mes_extenso[$mes];
}

function geraCodigoBarra($numero){

    $returno = "";
    $fino = 1;
    $largo = 3;
    $altura = 40;

    $barcodes[0] = '00110';
    $barcodes[1] = '10001';
    $barcodes[2] = '01001';
    $barcodes[3] = '11000';
    $barcodes[4] = '00101';
    $barcodes[5] = '10100';
    $barcodes[6] = '01100';
    $barcodes[7] = '00011';
    $barcodes[8] = '10010';
    $barcodes[9] = '01010';

    for($f1 = 9; $f1 >= 0; $f1--){
        for($f2 = 9; $f2 >= 0; $f2--){
            $f = ($f1*10)+$f2;
            $texto = '';
            for($i = 1; $i < 6; $i++){
                $texto .= substr($barcodes[$f1], ($i-1), 1).substr($barcodes[$f2] ,($i-1), 1);
            }
            $barcodes[$f] = $texto;
        }
    }

    $returno.= '<img src="' . IMG . '/barcode/p.gif" width="'.$fino.'" height="'.$altura.'" border="0" />';
    $returno.= '<img src="' . IMG . '/barcode/b.gif" width="'.$fino.'" height="'.$altura.'" border="0" />';
    $returno.= '<img src="' . IMG . '/barcode/p.gif" width="'.$fino.'" height="'.$altura.'" border="0" />';
    $returno.= '<img src="' . IMG . '/barcode/b.gif" width="'.$fino.'" height="'.$altura.'" border="0" />';

    $returno.= '<img ';

    $texto = $numero;

    if((strlen($texto) % 2) <> 0){
        $texto = '0'.$texto;
    }

    while(strlen($texto) > 0){
        $i = round(substr($texto, 0, 2));
        $texto = substr($texto, strlen($texto)-(strlen($texto)-2), (strlen($texto)-2));

        if(isset($barcodes[$i])){
            $f = $barcodes[$i];
        }

        for($i = 1; $i < 11; $i+=2){
            if(substr($f, ($i-1), 1) == '0'){
                $f1 = $fino ;
            }else{
                $f1 = $largo ;
            }

            $returno.= 'src="' . IMG . '/barcode/p.gif" width="'.$f1.'" height="'.$altura.'" border="0">';
            $returno.= '<img ';

            if(substr($f, $i, 1) == '0'){
                $f2 = $fino ;
            }else{
                $f2 = $largo ;
            }

            $returno.= 'src="' . IMG . '/barcode/b.gif" width="'.$f2.'" height="'.$altura.'" border="0">';
            $returno.= '<img ';
        }
    }
    $returno.= 'src="' . IMG . '/barcode/p.gif" width="'.$largo.'" height="'.$altura.'" border="0" />';
    $returno.= '<img src="' . IMG . '/barcode/b.gif" width="'.$fino.'" height="'.$altura.'" border="0" />';
    $returno.= '<img src="' . IMG . '/barcode/p.gif" width="1" height="'.$altura.'" border="0" />';

    return $returno;
}