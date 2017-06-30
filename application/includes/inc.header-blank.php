<?php 
/**
 * Cabeçalho que é exibido em branco na tela
 * 
 * @author Niury Martins - http://niurymartins.com.br
 */
require('inc.doctype.php'); ?>
<html  xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt-br">
<head>
	<title><?php echo SYSTEM_NAME;?> - Versão <?php echo VERSAO;?></title>
    
    <link rel="stylesheet" href="<?php echo CSS; ?>/default.css"  type="text/css" media="screen" /> 
    <link rel="stylesheet" href="<?php echo CSS; ?>/system.css"  type="text/css" media="screen" /> 
    <script type="text/javascript" src="<?php echo JS; ?>/jquery-1.4.2.min.js"></script>
    <?php
    if (isset($array_include_arq)) {
    	echo include_arq($array_include_arq);
    }
    ?>
</head>
<body>
	<div id="tudo">
		<div id="corpo"  style="display:block;">