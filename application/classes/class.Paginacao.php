<?php
/**
 * @author Niury Martins Pereira
 * @return String
 * @version 1.0
 * @copyright Niury Martins - 04/09/2009
 * @example 
 * $paginacao = new Paginacao();
 * $paginacao->set_site_link("pagina.php");
 * $paginacao->set_pagina_atual($_GET['pag']);
 * $paginacao->set_num_reg_pag(15);
 * $sql = "select count(*)  as num from tabela";
 * $sql = mysql_fetch_array(mysql_query($sql, $con));
 * $paginacao->set_num_reg_tot($sql['num']);
 * echo $paginacao->show();
 */

class Paginacao
{
	// Atributos da classe
	private $pagina_atual;     // Define quantas páginas serão navegáveis para os lados a partir da página atual
	private $num_pag_laterais;     // Define quantas páginas serão navegáveis para os lados a partir da página atual
	private $site_link;         // Define o hyperlink dos navegadores
	private $var_get;       // Define a variável do GET que receberá o número da página para navegar
	private $num_reg_pag; // Número de registros a ser mostrado  na tela
	private $num_reg_tot; // Número de Registros a serem paginados
	private $class_pag_atual;     // Classe CSS usada para a LABEL de página atual
	private $class_navegadores;   // Classe CSS usada para os LINKs de navegação
	private $class_separadores; // Classe CSS usada para os separadores
	private $class_div_paginacao; //classe CSS para div de paginação
	private $texto_anterior;   // Texto a ser mostrado no link para a página anterior
	private $texto_proxima;   // Texto a ser mostrado no link para a príxima página
	private $texto_separador;  // Texto a ser mostrado como separador para primeira e última páginas

	//Construtor
	function __construct()
	{
		$this->pagina_atual = 1;
		$this->num_pag_laterais = 3;
		$this->site_link = '';
		$this->var_get = 'pag';
		$this->num_reg_pag = 15;
		$this->num_reg = 0;
		$this->texto_separador = '...';
		$this->class_pag_atual = 'paginacao_atual';
		$this->class_navegadores = 'paginacao_navegar';
		$this->class_div_paginacao = 'paginacao';
		$this->texto_anterior = 'Anterior';
		$this->texto_proxima = 'Pr&oacute;xima';
		$this->class_navegadores = 'number';
		$this->class_separadores = 'sep';
		$this->class_div_paginacao = 'paginator';
		$this->class_pag_atual = 'now';
	}

	//seters
	public function set_pagina_atual($pag) {  $this->pagina_atual = ($pag>=1)?  (int)$pag  : 1 ; }
	public function set_site_link($url) {$this->site_link = $url; }
	public function set_var_get($get) { $this->var_get = $get; }
	public function set_num_reg_pag($num) { $this->num_reg_pag = $num; }
	public function set_class_div_paginacao($class) { $this->class_div_paginacao = $class; }
	public function set_class_pag_atual($class) { $this->class_pag_atual = $class; }
	public function set_class_navegadores($class) { $this->class_navegadores = $class; }
	public function set_texto_anterior($txt) { $this->texto_anterior = $txt; }
	public function set_texto_proxima($txt) { $this->texto_proxima = $txt; }
	public function set_num_reg_tot($num) {$this->num_reg_tot = $num; }
	public function set_num_pag_laterais($num) {$this->num_pag_laterais = $num; }
	public function set_class_separadores($class) {$this->class_separadores = $class; }
	public function set_texto_separador($txt) {$this->texto_separador = $txt; }

	//geters
	public function get_site_link() { return $this->site_link; }
	public function get_var_get() { return $this->var_get; }
	public function get_num_reg_pag() { return $this->num_reg_pag; }
	public function get_class_pag_atual() { return $this->class_pag_atual; }
	public function get_class_navegadores() { return $this->class_navegadores; }
	public function get_texto_anterior() { return $this->texto_anterior; }
	public function get_texto_proxima() { return $this->texto_proxima; }
	public function get_pagina_atual() {return $this->pagina_atual; }
	public function get_num_reg_tot() {return $this->num_reg_tot; }
	public function get_reg_ini() {return  (($this->get_pagina_atual()-1) * $this->get_num_reg_pag()); } // Define  a partir de qual registro que será exibido na tela
	public function get_ultima_pag(){ return ceil($this->get_num_reg_tot() / $this->get_num_reg_pag()); }
	public function get_num_pag_laterais(){ return $this->num_pag_laterais; }
	public function get_class_separadores(){ return $this->class_separadores; }
	public function get_texto_separador() {return $this->texto_separador; }

	public function __call($metodo, $paramentros){echo "O método <b>$metodo</b> não existe";}

	public function show()
	{
		// Monta o link
		if (strpos($this->get_site_link(), '?') === FALSE)
		{
			$link = $this->get_site_link() . '?' . $this->get_var_get() . '=';
		}
		else
		{
			$link = $this->get_site_link() . '&amp;' . $this->get_var_get() . '=';
		}

		// Verifica se tem navagação pra página anterior
		$anterior = '';
		if ($this->get_pagina_atual() > 1)
		{
			$anterior = '<a href="'.$link.($this->get_pagina_atual() - 1).'" class="'.$this->get_class_navegadores().'">'.$this->get_texto_anterior().'</a>';
		}


		// Verifica se mostra navegador para primeira página
		$primeira = '';
		if (($this->get_pagina_atual() - ($this->get_num_pag_laterais() + 1) > 1) && ($this->get_ultima_pag() > ($this->get_num_pag_laterais() * 2 + 2)))
		{
			$primeira = '<a href="'.$link.'1" class="'.$this->get_class_navegadores().'">1</a> <span class="'.$this->get_class_separadores().'">'.$this->get_texto_separador().'</span> ';
			$dec = $this->get_num_pag_laterais();
		}
		else
		{
			$dec = $this->get_pagina_atual();
			while ($this->get_pagina_atual() - $dec < 1)
			{
				$dec--;
			}
		}

		// Verifica se mostra navegador para última página
		$ultima = '';
		if (($this->get_pagina_atual() + ($this->get_num_pag_laterais() + 1) < $this->get_ultima_pag()) && ($this->get_ultima_pag() > ($this->get_num_pag_laterais() * 2 + 2)))
		{
			$ultima = '<span class="'.$this->get_class_separadores().'">'.$this->get_texto_separador().'</span> <a href="'.$link.$this->get_ultima_pag().'" class="'.$this->get_class_navegadores().'">'.$this->get_ultima_pag().'</a>';
			$inc = $this->get_num_pag_laterais();
		}
		else
		{
			$inc = $this->get_ultima_pag() - $this->get_pagina_atual();
		}

		// Se houverem menos páginas anteriores que o definido, tenta colocar mais páginas para a frente
		if ($dec < $this->get_num_pag_laterais())
		{
			$x = $this->get_num_pag_laterais() - $dec;
			while ($this->get_pagina_atual() + $inc < $this->get_ultima_pag() && $x > 0)
			{
				$inc++;
				$x--;
			}
		}
		// Se houverem menos páginas seguintes que o definido, tenta colocar mais páginas para trás
		if ($inc < $this->get_num_pag_laterais())
		{
			$x = $this->get_num_pag_laterais() - $inc;
			while (($this->get_pagina_atual() - $dec) > 1 && $x > 0)
			{
				$dec++;
				$x--;
			}
		}

		// Monta o conteúdo central do navegador
		$atual = '';
		for ($x = $this->get_pagina_atual() - $dec; $x <= $this->get_pagina_atual() + $inc; $x++)
		{
			$class = ($x == $this->get_pagina_atual())? $this->get_class_pag_atual() : $this->get_class_navegadores() ;
			$atual .= '<a href="'.$link.$x.'" class="'.$class.'">'.$x.'</a> ';
		}

		// Verifica se mostra navegador para próxima página
		$proxima = '';
		if ($this->get_pagina_atual() < $this->get_ultima_pag())
		{
			$proxima = '<a href="'.$link.($this->get_pagina_atual() + 1).'" class="'.$this->get_class_navegadores().'">'.$this->get_texto_proxima().'</a>';
		}
		unset($x, $dec, $inc, $class, $link);
		echo '<div class="'.$this->class_div_paginacao.'">';
		echo   ($anterior.$primeira.$atual.$ultima.$proxima);
		echo '</div>';
	}

	function __destruct()
	{
		unset($this->pagina_atual, $this->num_pag_laterais, $this->site_link, $this->var_get, $this->num_reg_pag, $this->num_reg_tot ,$this->class_pag_atual, $this->class_navegadores, $this->class_separadores, $this->texto_anterior, $this->texto_proxima, $this->texto_separador);
	}
}