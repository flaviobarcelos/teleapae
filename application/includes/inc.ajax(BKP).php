<?php
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
 * fução para salvar os dados da foto de uma oferta
 * 
 */
function salvar_dados_foto($cdfoto, $principal, $titulo, $tpfoto, $ordem = 0) {

	$titulo = addslashes(trim($titulo));
	$principal = addslashes(trim($principal));
	if(($principal == 'S' or $principal == 'N') and $titulo != '' and ($tpfoto == 'B' or $tpfoto == 'F' or $tpfoto == 'L')) {

		global $con;

		//caso seja logomarca, verifica se a empresa já possui logomarca
		if ($tpfoto == 'L') {
			$sql = "select cdempresa
					from tb_foto 
					where cdfoto = '$cdfoto'";
			$sql = mysql_query($sql, $con);
			$res = mysql_fetch_assoc($sql);
			$cdempresa = $res['cdempresa'];

			$sql = "select *
					from tb_foto 
					where tpfoto = 'L' and 
						  cdfoto <> '$cdfoto'";
			$sql = mysql_query($sql, $con);
			if (mysql_num_rows($sql)) {
				return '<span style="color:#FF0000;">A empresa já possui logomarca cadastrada</span>';
			}
		}

		$sql = "update tb_foto set
                titulo = '$titulo',
                principal = '$principal',
                tpfoto = '$tpfoto',
                ordem = '$ordem'
                where cdfoto = '$cdfoto'";
		if(mysql_query($sql, $con)) {
			return '<span style="color:green;">Dados alterados com sucesso</span>';
		}
		else {
			return '<span style="color:#FF0000;">' . MSG_ERROR . '</span>';
		}
	}

	return '<span style="color:#FF0000;">favor preencher corretamente os dados</span>';

}

/**
 * Retorna os options para um select das cidades que têm oferta
 *
 * @param int $cdestado
 */
function carregar_cidades_somente_oferta($cdestado) {
	global $con;

	//verifica se o usuário é franquia
	if (somenteFranquia()) {
		$where = " and tb_cidade.cdcidade in (" . getCidade() . ")";
	}
	else if(somenteLojista()) {
		$where = " and tb_cidade.cdcidade in (
            			    select distinct tb_oferta_cidade.cdcidade
            			    from tb_oferta_cidade, tb_oferta
            			    where tb_oferta.cdoferta = tb_oferta_cidade.cdoferta and
            			    	  tb_oferta.cdempresa in (".getCdempresas($_SESSION['logado']['usuario']['cdusuario']).")
        			    )";
	}

	if (!$cdestado) {
		$cdestado = 0;
	}

	$sql = "select distinct tb_cidade.*
			from tb_cidade, tb_oferta_cidade 
			where tb_cidade.cdestado in ($cdestado) and
				  tb_cidade.cdcidade = tb_oferta_cidade.cdcidade $where
			order by tb_cidade.nmcidade";
	$sql = mysql_query($sql, $con);

	if (mysql_num_rows($sql)) {
		$option = '';
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
 * Carregar ofertas das cidades
 *
 * @param int $cdcidade
 */
function carregar_ofertas_cidade($cdcidade) {
	global $con;

	if (!$cdcidade) {
		return '<option value="0">Selecione a cidade</option>';
	}

	//caso o usuário logado seja franquia
	if (somenteFranquia()) {
		$where = " and tb_oferta_cidade.cdcidade in (" . $cdcidade . ")";
	}
	//caso seja lojista
	else if (somenteLojista()) {
		$where = " and tb_oferta.cdempresa in (" . getCdempresas($_SESSION['logado']['usuario']['cdusuario']) . ")
		           and tb_oferta_cidade.cdcidade in (" . $cdcidade . ")";
	}
	else {
		$where = " and tb_oferta_cidade.cdcidade in (" . $cdcidade . ")";
	}

	$sql = "select distinct
				tb_oferta.cdoferta, 
				tb_oferta.nmoferta
			from 
				tb_oferta, 
				tb_oferta_cidade
			where 
				tb_oferta.cdoferta = tb_oferta_cidade.cdoferta and
				tb_oferta.excluido = 'N' $where
			order by 
				tb_oferta.nmoferta";

	$sql = mysql_query($sql, $con);

	if (!mysql_num_rows($sql)) {
		return '<option value="0">Selecione a cidade</option>';
	}

	$options = '';

	while ($res = mysql_fetch_assoc($sql)) {
		array_walk($res, 'array_var_show');
		$options .= '<option value="'.$res['cdoferta'].'">'.$res['nmoferta'].'</option>';
	}

	return $options;
}

/**
 * Retorna os options contendo as subcategorias de uma empresa
 *
 * @param int $cdempresa
 * @return string
 */
function carregar_categorias_empresa($cdempresa) {
	global $con;

	$return = '';
	$sql = "select
				distinct tb_categoria.cdcategoria, tb_categoria.nmcategoria
			from 
				tb_categoria,
			    tb_empresa_categoria
			where 
			    tb_empresa_categoria.cdcategoria = tb_categoria.cdcategoria and
				tb_empresa_categoria.cdempresa = '$cdempresa'
			order 
				by tb_categoria.nmcategoria";

	$sql = mysql_query($sql, $con);
	if (mysql_num_rows($sql)) {

		while ($res = mysql_fetch_assoc($sql)) {
			$return .= '<tr><td>';
			$return .= '<input type="checkbox" name="cdcategoria[]" value="' . $res['cdcategoria'] . '" />';
			$return .= $res['nmcategoria'];
			$return .= '</td></tr>';
		}

		/*
		if (mysql_num_rows($sql)) {
		while ($res = mysql_fetch_assoc($sql)) {
		$return = '<tr><td style="background:#CCC;"><strong>' . $res['nmcategoria'] . '</strong></td></tr>';
		$sql_sub = "select
		tb_subcategoria.cdsubcategoria, tb_subcategoria.nmsubcategoria
		from
		tb_subcategoria,
		tb_empresa_subcategoria
		where
		tb_subcategoria.cdsubcategoria = tb_empresa_subcategoria.cdsubcategoria and
		tb_empresa_subcategoria.cdempresa = '$cdempresa' and
		tb_subcategoria.cdcategoria = '$res[cdcategoria]'
		order by tb_subcategoria.nmsubcategoria";

		$sql_sub = mysql_query($sql_sub, $con);
		while ($res_sub = mysql_fetch_assoc($sql_sub)) {
		$return .= '<tr><td>';
		$return .= '<input type="checkbox" name="cdsubcategoria[]" value="' . $res_sub['cdsubcategoria'] . '" />';
		$return .= $res_sub['nmsubcategoria'];
		$return .= '</td></tr>';
		}
		*/
	}
	else {
		$return = '<tr><td><strong>Selecione a empresa</strong></td></tr>';
	}

	return $return;
}

/**
 * Retorna os options de subcategorias de noticias
 *
 * @param int $cdcategorianoticia
 * @return string
 */
function carregar_subcategorias_noticia($cdcategorianoticia) {
	global $con;
	$sql = "select *
			from tb_subcategorianoticia
			where cdcategorianoticia = '$cdcategorianoticia'
			order by nmsubcategorianoticia";
	$sql = mysql_query($sql, $con);
	$option = '<option value="0">Selecione</option>';
	
	while ($res = mysql_fetch_assoc($sql)) {
		$option .= '<option value="' . $res['cdsubcategorianoticia'] . '">' . $res['nmsubcategorianoticia'] . '</option>';
	}
	
	return $option;
}


/**
 * Carrega as empresas conforme o seu tipo
 *
 * @param int $cdtpempresa
 * @return string
 */
function carregar_empresa($cdtpempresa) {
	global $con;
	
	if (in_array('RE', $_SESSION['logado']['usuario']['tpusuario']['sgtpusuario']) or 
		in_array('CV', $_SESSION['logado']['usuario']['tpusuario']['sgtpusuario'])) {
		$aux = " and tb_empresa.cdcidade in (" . getCidade() . ") ";
	}
	
	$sql = "select 
				tb_empresa.*
			from 
				tb_empresa
			where 
				tb_empresa.cdtpempresa = '$cdtpempresa' $aux
			order by 
				tb_empresa.nmempresa";
	$sql = mysql_query($sql, $con);
	
	$option = '<option value="0">Selecione</option>';
	
	while ($res = mysql_fetch_assoc($sql)) {
		array_walk($res, 'array_var_show');
		$option .= '<option value="' . $res['cdempresa'] . '">' . ($res['nmempresa']) . '</option>';
	}
	
	return $option;
}


/**
 * Identifica a requisição
 */
if (isset($_GET['carregar_cidades'])) {
	echo carregar_cidades($_GET['cdestado']);
}
else if(isset($_GET['salvar_dados_foto']) and isset($_GET['cdfoto']) and
isset($_GET['titulo']) and isset($_GET['foto_principal']) and isset($_GET['tpfoto'])) {
	echo salvar_dados_foto($_GET['cdfoto'], $_GET['foto_principal'], $_GET['titulo'], $_GET['tpfoto'], $_GET['ordem']);
}
else if (isset($_GET['carregar_cidades_somente_oferta'])) {
	echo carregar_cidades_somente_oferta($_GET['cdestado']);
}
else if (isset($_GET['carregar_ofertas_cidade']) and isset($_GET['cdcidade'])) {
	echo carregar_ofertas_cidade($_GET['cdcidade']);
}
else if (isset($_GET['carregar_categorias_empresa']) and isset($_GET['cdempresa'])) {
	echo carregar_categorias_empresa($_GET['cdempresa']);
}
else if (isset($_GET['carregar_subcategorias_noticia']) and isset($_GET['cdcategorianoticia'])) {
	echo carregar_subcategorias_noticia($_GET['cdcategorianoticia']);
}
else if (isset($_GET['carregar_empresa']) and isset($_GET['cdtpempresa'])) {
	echo carregar_empresa($_GET['cdtpempresa']);
}