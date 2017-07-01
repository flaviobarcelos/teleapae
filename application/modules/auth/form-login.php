<?php 

/**
 * Formul�rio de login
 * - Criado em 07/09/2010
 * 
 * @author Niury Martins Pereira
 */
require(INCLUDES . '/inc.doctype.php');

?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt-br">
	<head>
		<meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />
		<title><?php echo 'Formul�rio de Login | ' , SYSTEM_NAME;?></title>
		<link rel="stylesheet" href="<?php echo CSS , '/default.css'; ?>" type="text/css" media="screen" />
		<link rel="stylesheet" href="<?php echo CSS , '/form-login.css'; ?>" type="text/css" media="screen" />
		<script type="text/javascript" src="<?php echo JS . '/jquery-1.4.2.min.js';?>"></script>
		<script type="text/javascript" src="<?php echo JS;?>/jquery.validate.js"></script>
		<script type="text/javascript" src="<?php echo JS;?>/jquery.blockui.js"></script>		
		<script type="text/javascript">
		//<![CDATA[

		$.validator.setDefaults({
			submitHandler: function() {
				
				//verifica se � vendedor padr�o
				
				$.ajax({
					type: "POST",
					url: "<?php echo SERVER;?>/?auth&script-login",
					data: "enviar=1&login=" + $('#login').val() + "&senha=" + $('#senha').val(),
					beforeSend: function() {
						$.growlUI('Autenticando', 'Aguarde...');
					},
					success: function(txt) {

						var obj = jQuery.parseJSON(txt);

						if(obj.retorno) {

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
								message: 'Autentica��o realizada com sucesso.<br />Carregando o sistema...',
								onUnblock: function() {
									window.location.href = "<?php echo SERVER . '/' . montalink('default-home', '&');?>";
								}
							});
						}
						else {
							$.growlUI('', ('<h4>' + obj.txt + '</h4>'));
						}
					},
					error: function(txt) {
						alert('Desculpe, houve um erro interno.');
					}
				});

			}
		});

		$(document).ready( function() {
			$("#form-login").fadeIn(500);
			$("#login").focus();

			var validator = $("#form-login").bind("invalid-form.validate", function() {
				//$.growlUI('Aten��o', 'Preencha corretamente os dados');

				$.blockUI({
					message: '<h4>Favor preencher os dados corretamente</h4>',
					fadeIn: 700,
					fadeOut: 700,
					timeout: 2000,
					showOverlay: false,
					centerY: false,
					css: {
						width: '350px',
						top: '10px',
						left: '',
						right: '10px',
						border: 'none',
						padding: '5px',
						backgroundColor: '#000',
						'-webkit-border-radius': '10px',
						'-moz-border-radius': '10px',
						opacity: .6,
						color: '#fff'
					}
				});


			}).validate({

				rules:{
					login:{
						required: true
					},
					senha:{
						required: true
					}
				},
				messages:{
					login:{
						required: null
					},
					senha:{
						required: null
					}
				}
			});

			$('#vendedor').click( function() {
				if ($('#vendedor').is(':checked')) {
					$('#login').attr('disabled', true);
					$('#senha').attr('disabled', true);
				}
				else {
					$('#login').removeAttr('disabled', true);
					$('#senha').removeAttr('disabled', true);
				}
			});
		});
		//]]>
		</script>
	</head>
	<body>
		<form id="form-login" style="display:none;" method="post" action="">
			<p style="margin-top:30px;">
				<label for="login">Usu�rio</label>
				<br />
				<input type="text" maxlength="16" class="input-text" name="login" id="login" />
			</p>
			<p style="margin-top:0;">
				<label for="senha">Senha</label>
				<br />
				<input type="password" maxlength="16" class="input-text" name="senha" id="senha" />
				<input type="submit" value="Enviar" name="enviar" id="enviar" style="margin-top:10px;" class="buttom" />
			</p>
			<!--
			<p class="remenber-password">
				<a href="?default&amp;recuperar-senha">Esqueceu a senha?</a>
			</p>
			-->
		</form>
	</body>
</html>