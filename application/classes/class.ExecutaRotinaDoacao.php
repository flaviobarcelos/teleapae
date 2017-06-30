<?php
/**
 * Classe responsavel por executar a rotina de inserção de doações
 * - Criado em 11/12/2013 16:41
 */

class RotinaDoacao {

	public $mes;
	public $ano;
	private $con;

	public function __construct($con, $mes, $ano) {
		$this->con = $con;
		$this->ano = $ano;
		$this->mes = $mes < 10 ? "0" . $mes : $mes ;
	}

	public function ExecutaRotina() {

		/* seleciona todos os doadores que são fidelizados e que não possuem
		lançamento de doação no mês em questão */
		$sql = "select
				      tb_doador.cddoador,
				      tb_doador.nmresponsavel,
				      tb_doador.diarec,
				      tb_doador.vldoacao,
				      tb_tpdoador.sgtpdoador,
				      tb_tpdoador.qtdmes
				from 
				     tb_doador
				     inner join tb_tpdoador on tb_doador.cdtpdoador = tb_tpdoador.cdtpdoador
				     left join tb_doacao on (tb_doador.cddoador = tb_doacao.cddoador and tb_doacao.excluido = 'N')
				where 
				      tb_tpdoador.sgtpdoador in ('F') and
				      tb_doador.ativo = 'S' and
				      tb_doador.diarec > 0
				group by
				      tb_doador.cddoador,
				      tb_doador.nmresponsavel,
				      tb_doador.diarec,
				      tb_doador.vldoacao,
				      tb_tpdoador.sgtpdoador,
				      tb_tpdoador.qtdmes
				having 
				      (not exists (select cddoador
				                  from tb_doacao 
				                  where tb_doador.cddoador = tb_doacao.cddoador and
				                        date_format(tb_doacao.dtrec, '%m-%Y') = '{$this->mes}-{$this->ano}' and
				                        tb_doacao.excluido = 'N'
				                  ))
				      or
		      		  count(tb_doacao.cddoacao) <= 0
                                                     
                                                     
           		union

				select
				      tb_doador.cddoador,
				      tb_doador.nmresponsavel,
				      tb_doador.diarec,
				      tb_doador.vldoacao,
				      tb_tpdoador.sgtpdoador,
				      tb_tpdoador.qtdmes
				from 
				     tb_doador
				     inner join tb_tpdoador on tb_doador.cdtpdoador = tb_tpdoador.cdtpdoador
				     left join tb_doacao on (tb_doador.cddoador = tb_doacao.cddoador and tb_doacao.excluido = 'N')
				where 
				      tb_tpdoador.sgtpdoador in ('B', 'T') and
				      tb_doador.ativo = 'S' and
				      tb_doador.diarec > 0
				group by
				      tb_doador.cddoador,
				      tb_doador.nmresponsavel,
				      tb_doador.diarec,
				      tb_doador.vldoacao,
				      tb_tpdoador.sgtpdoador,
				      tb_tpdoador.qtdmes
				having 
				      (date_format( date_add( max(tb_doacao.dtrec), INTERVAL tb_tpdoador.qtdmes MONTH), '%m-%Y') = '{$this->mes}-{$this->ano}' and 
				       not exists (select cddoador
				                  from tb_doacao 
				                  where tb_doador.cddoador = tb_doacao.cddoador and
				                        date_format(tb_doacao.dtrec, '%m-%Y') = '{$this->mes}-{$this->ano}' and
				                        tb_doacao.excluido = 'N'
				                  ))
				      or
		      		  count(tb_doacao.cddoacao) <= 0";
		//mostra_array($sql);
		//exit();
		$sql = mysql_query($sql, $this->con);
		
		//insere as doações
		$count = 0;
		while ($res = mysql_fetch_assoc($sql)) {
			
			$dtrec = $this->ano . '-' . $this->mes . '-' . $res['diarec'];
			
			$sql_insert = "insert into tb_doacao
					(cddoador, dtrec, 
					 vldoacao, sgtpdoador, job,
					 nmresponsavel,
					 nmfantasia, cdusuario)
					values
					('$res[cddoador]', '$dtrec',
					 '$res[vldoacao]', '$res[sgtpdoador]', 'S',
					 '" . addslashes($res['nmresponsavel']) . "', 
					 '" . addslashes($res['nmfantasia']) . "', '{$_SESSION['logado']['usuario']['cdusuario']}')";
			//die(mostra_array($sql_insert));
			mysql_query($sql_insert, $this->con) or die(mysql_error($this->con));
			
			$count++;
		}
		
		//log da rotina
		$sql = "insert into tb_logdoacao
				(dtlog, totdoacao, 
				 nmusuario, mes, ano)
				values
				('" . date('Y-m-d H:i:s') . "', $count, 
				 '{$_SESSION['logado']['usuario']['nmusuario']}', '{$this->mes}', '{$this->ano}')";
		mysql_query($sql, $this->con) or die(mysql_error($this->con));
		
		
		//atualiza o total de doações de todos os doadores
		$sql = "update tb_doador set 
       			vltotdoacao = (select sum(tb_doacao.vldoacao) as tot 
       						   from tb_doacao where tb_doacao.cddoador = tb_doador.cddoador) 
				where tb_doador.ativo = 'S'";
		mysql_query($sql, $this->con);
		
		return $count;
	}
}