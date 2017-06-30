<?php 
setlocale(LC_ALL, 'pt_BR');
header("Content-Type: text/html; charset=ISO-8859-1", true);
require('inc.doctype.php'); 
?>
<html  xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt-br">
<head>
	<title><?php echo SYSTEM_NAME;?> - Versão <?php echo VERSAO;?></title>
    <meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />
    
    <link rel="stylesheet" href="<?php echo CSS; ?>/default.css"  type="text/css" media="screen" /> 
    <link rel="stylesheet" href="<?php echo CSS; ?>/system.css"  type="text/css" media="screen" /> 
    
    <script type="text/javascript" src="<?php echo JS;?>/jquery-1.4.2.min.js"></script>
    <script type="text/javascript" src="<?php echo JS;?>/jquery.blockui.js"></script>
    <script type="text/javascript">
    //<![CDATA[
    $(document).ready(function(){
    	$("#corpo").fadeIn(500);

    	$("#sair").click(function(){
    		if (confirm('Deseja realmente sair do sistema?')) {
    			$.blockUI({
    				css: {
    					border: 'none',
    					padding: '15px',
    					backgroundColor: '#000',
    					'-webkit-border-radius': '10px',
    					'-moz-border-radius': '10px',
    					opacity: 0.5,
    					color: '#fff',
    					'font-weight': 'bold',
    					'font-size' : '18px'
    				},
    				fadeIn: 100,
    				timeout: 2000,
    				message: 'Saindo do sistema<br />Aguarde...',
    				onUnblock: function() {
    					window.location.href = "?auth&script-logout";
    				}
    			});
    			return true;
    		}
    		else {
    			return false;
    		}
    	});
    });
    //]]>
    </script>
    <?php

    if (isset($array_include_arq)) {
    	echo include_arq($array_include_arq);
    }
    ?>
</head>
<body>
	<div id="tudo">
		<div id="cabecalho">
			<div id="banner">
			   	<div class="clear"></div>
				<div id="logado">
					<?php
					//escreve os dados do usuário
					echo 'Seja bem vindo(a), ' ,
					$_SESSION['logado']['usuario']['nmusuario'],
					' [<a href="' , montalink('meus-dados') , '">Meus dados</a>] ',
					'[<a id="sair" onclick="return false;" href="' , montalink('script-logout') , '">Sair</a>]';
					?>
				</div>
				<div id="data"><?php echo strftime("%A, %d de %B de %Y", strtotime(date('Y/m/d')));?></div>
			</div>
		</div>
		<div id="menu_top">	
            <div id="menu_top_itens">
                <ul>
                	<?php
                	$tppaginas = getTppaginas();
                	if (in_array('DEFAULT', $tppaginas)) {
                		echo '<li><a href="' , montalink('default-home') , '">Home</a></li>';
                	}

                	if (in_array('ADMINISTRACAO', $tppaginas)) {
                		echo '<li><a href="' , montalink('administracao') , '">Administração</a></li>';
                	}

                	if (in_array('CADASTRO', $tppaginas)) {
                		echo '<li><a href="' , montalink('cadastros') , '">Cadastros</a></li>';
                	}

                	if (in_array('CONSULTA', $tppaginas)) {
                		echo '<li><a href="' , montalink('consultas') , '">Consultas</a></li>';
                	}

                	if (in_array('RELATORIO', $tppaginas)) {
                		echo '<li><a href="' , montalink('relatorios') , '">Relatórios</a></li>';
                	}

                	if (in_array('GRAFICO', $tppaginas)) {
                		echo '<li><a href="' , montalink('graficos') , '">Gráficos</a></li>';
                	}

                	if (in_array('CONFIGURACAO', $tppaginas)) {
                		echo '<li><a href="' , montalink('configuracoes') , '">Configurações</a></li>';
                	}

                	if (in_array('ROTINA', $tppaginas)) {
                		echo '<li><a href="' , montalink('rotinas') , '">Rotinas</a></li>';
                	}

                	if (in_array('TEMPORARIO', $tppaginas)) {
                		echo '<li><a href="' , montalink('temporario') , '">Temporário</a></li>';
                	}
                    ?>
                </ul>
			</div><?php //id="menu_top_itens" ?>
		</div>
		<div id="corpo">