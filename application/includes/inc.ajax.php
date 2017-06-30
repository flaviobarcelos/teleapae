<?php
header("Content-Type: text/html;  charset=ISO-8859-1",true);

/**
 * Arquivo com as funções AJAX utilizadas no sistema
 * 
 * @author Niury Martins - http://niurymartins.com.br
 */

/**
 * Retorna para um option as cidades de um estado
 *
 * @param int $cdestado
 */
function carregar_cidades($cdestado) {
	global $con;

	if (!$cdestado) {
		return '<option value="0">:.Selecione o estado.:</option>';
	}

	//verifica se o usuário é franquia
	if (somenteRep()) {
		$where = " and tb_cidade.cdcidade in (" . getCidade() . ")";
	}
	else {
		$where = '';
	}

	$sql = "select *
			from tb_cidade 
			where cdestado in ($cdestado) $where
			order by nmcidade";
	$sql = mysql_query($sql, $con);

	if (mysql_num_rows($sql)) {
		$option = '<option value="0">:.Selecione a cidade.:</option>';
		$x = 1;
		while ($res = mysql_fetch_assoc($sql)) {
			array_walk($res, 'array_var_show');
			$class = ++$x % 2 != 0 ? 'lista_par' : 'lista_impar' ;
			$option .= '<option class="' . $class . '" value="' . $res['cdcidade'] . '">' . $res['nmcidade'] . '</option>';
		}

		return $option;
	}

	return '<option value="0">:.Nenhuma cidade encontrada.:</option>';
}

/**
 * Retorna os bairros de uma cidade
 *
 * @param int $cdcidade
 * @return string
 */
function getbairroscidade($cdcidade) {
	global $con;

	$sql = "select distinct nmbairro
			from tb_doador
			where tb_doador.cdcidade = '$cdcidade'
			order by nmbairro";
	$sql = mysql_query($sql, $con);

	$return = '';
	while ($res = mysql_fetch_assoc($sql)) {
		$return .= '<input type="checkbox" name="nmbairro[]" value="' . $res['nmbairro'] . '" /> ' . $res['nmbairro'] . '<br />';
	}

	return $return;
}

/**
 * Retorna os nomes dos doadores
 *
 * @param string $q
 * @return void
 */
function getDoadorByNome($q) {
	global $con;

	//echo ($q);
	$q = utf8_decode(var_insert_db($q));
	//exit();

	if (strlen($q) >= 3) {

		$sql = "select
					cddoador, nmresponsavel
				from 
					tb_doador
				where 
					nmresponsavel like '$q%'
				order by 
					nmresponsavel
				limit 20";
		$sql = mysql_query($sql, $con);

		$return = '';
		while ($res = mysql_fetch_assoc($sql)) {
			//array_walk($res, 'array_var_show');
			echo "$res[nmresponsavel] -- $res[cddoador]\n";
		}

		/*
		echo "var doadores = [";
		$x = 0;
		while ($res = mysql_fetch_assoc($sql)) {
		if ($x > 0) {
		echo ",";
		}

		echo "{cddoador: \"$res[cddoador]\", name: \"$res[nmresponsavel]\"}";
		$x++;
		}

		echo "];";
		*/
	}
}

/**
 * Retorna os dados do doador pelo nome
 *
 * @param string $q
 */
function getDoadorJsonByNome($q) {
	global $con;

	$q = utf8_decode(var_insert_db($q));
	if ($q) {
		$q = $q;
		$q = split(' -- ', $q);
		$cddoador = $q[1];
		$nmresp = $q[0];
	}
	
	if ($cddoador) {

		$sql = "select
					cddoador, 
					nmresponsavel, 
					obsrecibo,
					diarec,
					vldoacao
				from 
					tb_doador
				where 
					cddoador = '$cddoador'";
		$sql = mysql_query($sql, $con);

		while ($res = mysql_fetch_assoc($sql)) {
			
			if ($res['diarec']) {
				if ($res['diarec'] >= 1 and $res['diarec'] <= 9) {
					$res['diarec'] = '0'.$res['diarec'];
				}
				
				$dtrec = $res['diarec'] . '/' . date('m/Y');
			}
			else {
				$dtrec = '';
			}
			
			$return = array('cddoador' => $res['cddoador'], 
			'nmresponsavel' => utf8_encode($res['nmresponsavel']), 
			'obsrecibo' => utf8_encode($res['obsrecibo']),
			'vldoacao' => number_show($res['vldoacao']),
			'diarec' => $res['diarec'],
			'dtrec' => $dtrec);
		}
		

		return json_encode($return);
	}
}


/**
 * Identifica a requisição
 */
if (isset($_GET['carregar_cidades'])) {
	echo carregar_cidades($_GET['cdestado']);
}
else if (isset($_POST['getbairroscidade']) and (int)$_POST['cdcidade']) {
	echo getbairroscidade((int)$_POST['cdcidade']);
}
else if (isset($_REQUEST['getdoadorbynome']) and isset($_REQUEST['q'])) {
	echo getDoadorByNome($_REQUEST['q']);
}
else if (isset($_POST['getDoadorJsonByNome']) and $_POST['nmresp']) {
	echo getDoadorJsonByNome($_POST['nmresp']);
}