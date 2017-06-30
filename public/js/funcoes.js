function confirma(texto) {
	texto = (texto==0)? 'Deseja realmente excluir estes dados?' : texto ;
	return resposta=confirm(texto);
}

function sonumero(evtKeyPress) {
	var nTecla;
	nTecla = (evtKeyPress.which) ? evtKeyPress.which : evtKeyPress.keyCode;
	if((nTecla > 47 && nTecla < 58) || nTecla == 9) {
		return true;
	}
	else {
		if (nTecla != 8) {
			return false;
		}
		else {
			return true;
		}
	}
}

/*
Funções para manipular dados em selects
são utilizadas nos filtros de relatórios
*/
function monta_campo_hidden(list, hidden) {
	var i;
	hidden.value = "";
	if(list.options.length) {
		for (i = 0; i < list.options.length ; i++) {
			hidden.value += (hidden.value == "")? list.options[i].value : ","+list.options[i].value;
		}
		return hidden.value;
	}
	return 0;
}

//realiza somente a função do getElementById
function getcampo(campo) {
	return document.getElementById(campo);
}

function TrocaList(ListOrigem,ListDestino, tudo)
{
	var i;
	for (i = 0; i < ListOrigem.options.length ; i++)
	{
		if(tudo)
		{
			var Op = document.createElement("option");
			Op.text = ListOrigem.options[i].text;
			Op.value = ListOrigem.options[i].value;
			ListDestino.options.add(Op);
			ListOrigem.remove(i);
			i--;
		}
		else
		{
			if (ListOrigem.options[i].selected == true)
			{
				var Op = document.createElement("option");
				Op.text = ListOrigem.options[i].text;
				Op.value = ListOrigem.options[i].value;
				ListDestino.options.add(Op);
				ListOrigem.remove(i);
				i--;
			}
		}
	}
	ListDestino.disabled = (ListDestino.options.length>0)? false : true ;
}

function limpa_option_selecionado(id_campo) {
	var i;
	while(document.getElementById(id_campo).options.length > 0) {
		document.getElementById(id_campo).remove(document.getElementById(id_campo).options.length--);
	}
}

/**
* função utilizada para marcar vários checks
*/
function marca_check(form, campo_check, nmcheck){
	for (i = 0; i < form.elements.length; i++) {
		if(form.elements[i].type == "checkbox" && form.elements[i].name == nmcheck) {
			form.elements[i].checked = campo_check.checked;
		}
	}
}

function carregar_cidades(cdestado, id_retorno) {
	url = "?default&ajax&carregar_cidades&cdestado=" + cdestado;
	ajaxGet(url, document.getElementById(id_retorno), true);
}

/**
* carrega somente as cidades que têm ofertas
*/
function carregar_cidades_somente_oferta(cdestado, id_retorno) {
	url = "?default&ajax&carregar_cidades_somente_oferta&cdestado=" + cdestado;
	ajaxGet(url, document.getElementById(id_retorno), true);
}

function carregar_ofertas_cidade(cdcidade, id_retorno) {
	url = "?default&ajax&carregar_ofertas_cidade=true&cdcidade=" + cdcidade;
	ajaxGet(url, document.getElementById(id_retorno), true);
}

//salva os dados de uma foto de uma oferta ou empresa
function salvar_dados_foto(cdfoto) {

	titulo = document.getElementById('titulo' + cdfoto);
	foto_principal = document.getElementById('foto_principal' + cdfoto);
	tpfoto = document.getElementById('tpfoto' + cdfoto);
	ordem = document.getElementById('ordem' + cdfoto).value;
	elemento_retorno = document.getElementById('elemento_retorno' + cdfoto);

	url = '?default&ajax&&salvar_dados_foto&cdfoto='+cdfoto+'&titulo='+titulo.value.trim()+'&foto_principal='+foto_principal.value+'&tpfoto='+tpfoto.value+'&ordem='+ordem;
	ajaxGet(url, elemento_retorno, true);
}

//carrega as categorias de uma empresa em um select
function carregar_categorias_empresa(cdempresa) {
	elemento_retorno = document.getElementById('tablecategorias');
	url = '?default&ajax&carregar_categorias_empresa&cdempresa='+cdempresa;
	ajaxGet(url, elemento_retorno, true);
}

//carrega as subcategorias das categorias de notícias
function carregar_subcategorianoticia(cdcategorianoticia) {
	elemento_retorno = document.getElementById('cdsubcategorianoticia');
	url = '?default&ajax&carregar_subcategorias_noticia&cdcategorianoticia='+cdcategorianoticia;
	ajaxGet(url, elemento_retorno, true);
}

//carrega as empresas conforme seu tipo
function carregar_empresa(cdtpempresa) {
	elemento_retorno = document.getElementById('cdempresa');
	url = '?default&ajax&carregar_empresa&cdtpempresa='+cdtpempresa;
	ajaxGet(url, elemento_retorno, true);
}