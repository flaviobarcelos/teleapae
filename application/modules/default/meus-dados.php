<?php 

/**
 * Formulário de alteração de dados e senha
 * O acesso é feito pelo próprio usuário
 * 
 * @author Niury Martins - http://niurymartins.com.br
 */

require(INCLUDES . '/inc.header.php'); ?>
<div class="nav">
    > Meus Dados
</div>

<fieldset style="float:left; width:550px;">
    <legend>Meus Dados</legend>
    Caso necessite que seja alterado algum de seus dados você deverá enviar um 
    e-mail para <strong><?php echo EMAIL_SUPORTE;?></strong> solicitando tais alterações.
    <?php
    $sql = "select sis_tpusuario.*
    		from 
    			sis_usuario_tpusuario, sis_tpusuario
    		where 
    			sis_usuario_tpusuario.cdusuario=" . $_SESSION['logado']['usuario']['cdusuario'] . ' and 
    			sis_usuario_tpusuario.cdtpusuario = sis_tpusuario.cdtpusuario
    		order by sis_tpusuario.nmtpusuario';
    $sql = mysql_query($sql, $con_sma);

    $txt = '';
    while ($res = mysql_fetch_assoc($sql)) {

    	if ($txt != '') {
    		$txt .= ', ';
    	}
    	$txt .= $res['nmtpusuario'];
    }
    ?> 
    <p style="margin-top:15px;">
        <label style="width:90px;">Tipo(s):</label>
            <input type="text" readonly="readonly" value="<?php echo $txt;?>" name="dstpusuario" style="width:350px;" />
            <br />
        <label style="width:90px;">Nome:</label>
            <input type="text" readonly="readonly" value="<?php echo $_SESSION['logado']['usuario']['nmusuario'];?>" name="nmusuario" style="width:350px;" />
            <br />
        <label style="width:90px;">E-mail:</label>
            <input type="text" readonly="readonly" value="<?php echo $_SESSION['logado']['usuario']['email'];?>" name="email" style="width:350px;" />
            <br />
        <label style="width:90px;">Usuário:</label>
            <input type="text" style="width:100px;" readonly="readonly" name="telefone_residencial" value="<?php echo $_SESSION['logado']['usuario']['login']; ?>" />
            <br />
    </p>
</fieldset>

<?php if ($_SESSION['logado']['usuario']['alterar_senha'] == 'S') { 

	echo '<fieldset style="float:left; margin-left:15px; width:370px;">';

	if(isset($_POST['trocar_senha']))
	{
		array_walk($_POST, 'array_insert_db');
		extract($_POST);

		$senha_atual = $senha_atual;
		$nova_senha = $nova_senha;
		$conf_nova_senha = $conf_nova_senha;

		if($senha_atual != '' and $nova_senha != '' and $conf_nova_senha != '')
		{
			$senha_atual = char_senha($senha_atual);
			$nova_senha = char_senha($nova_senha);
			$conf_nova_senha = char_senha($conf_nova_senha);

			$sql = 'select senha
	            from sis_usuario 
	            where cdusuario='.$_SESSION['logado']['usuario']['cdusuario'];

			extract(mysql_fetch_array(mysql_query($sql, $con_sma)));

			if($senha != $senha_atual)
			{
				set_msg($msg, 'A senha atual está incorreta');
			}

			if($nova_senha != $conf_nova_senha)
			{
				set_msg($msg, 'A confirmação da nova senha está incorreta');
			}

			if($nova_senha=='' or $conf_nova_senha='')
			{
				set_msg($msg, 'Favor preencher a nova senha e a confirmação da mesma');
			}

			if(!isset($msg))
			{
				$sql = "update sis_usuario set senha='" . $nova_senha . "' where cdusuario=".$_SESSION['logado']['usuario']['cdusuario'];
				if($sql = mysql_query($sql, $con_sma))
				{
					echo msg('ok', 'Senha alterada com sucesso.');
				}
			}
			else echo msg('alert',$msg);
		}
		else echo msg('alert','Favor preencher corretamente todos os campos');
	}
?>
    <legend>Minha Senha</legend>
    <form method="post" action="?default&amp;meus-dados">
        <p>
            <?php echo label('Senha Atual','senha_atual',true); ?>
            <input type="password" style="width:200px;" maxlength="16" name="senha_atual" id="senha_atual" />   
            <br />  
             <?php echo label('Nova Senha','nova_senha',true); ?>
            <input type="password" style="width:200px;" maxlength="16" name="nova_senha" id="nova_senha" />   
            <br />  
             <?php echo label('Repita a nova senha','r_nova_senha',true); ?>
            <input type="password" style="width:200px;" maxlength="16" name="conf_nova_senha" id="r_nova_senha" />           
        </p>
        <p style="text-align:center; clear:both;">
            <input type="submit" name="trocar_senha" value="Enviar" class="botao" />
        </p>
    </form>
</fieldset>
<?php
}
require(INCLUDES . '/inc.footer.php'); ?>