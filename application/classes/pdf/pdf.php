<?php

include_once('fpdf.php');

/**
 * Extens�o da classe fdf para inclus�o de n�meros nas p�ginas
 *
 */

class PDF extends FPDF
{
	var $B=0;
	var $I=0;
	var $U=0;
	var $HREF='';
	var $ALIGN='';

	function WriteHTML($html)
	{
		//HTML parser
		$html=str_replace("\n",' ',$html);
		$a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
		foreach($a as $i=>$e)
		{
			if($i%2==0)
			{
				//Text
				if($this->HREF)
				$this->PutLink($this->HREF,$e);
				elseif($this->ALIGN=='center')
				$this->Cell(0,5,$e,0,1,'C');
				else
				$this->Write(5,$e);
			}
			else
			{
				//Tag
				if($e[0]=='/')
				$this->CloseTag(strtoupper(substr($e,1)));
				else
				{
					//Extract properties
					$a2=explode(' ',$e);
					$tag=strtoupper(array_shift($a2));
					$prop=array();
					foreach($a2 as $v)
					{
						if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
						$prop[strtoupper($a3[1])]=$a3[2];
					}
					$this->OpenTag($tag,$prop);
				}
			}
		}
	}

	function OpenTag($tag,$prop)
	{
		//Opening tag
		if($tag=='B' || $tag=='I' || $tag=='U')
		$this->SetStyle($tag,true);
		if($tag=='A')
		$this->HREF=$prop['HREF'];
		if($tag=='BR')
		$this->Ln(5);
		if($tag=='P')
		$this->ALIGN=$prop['ALIGN'];
		if($tag=='HR')
		{
			if( !empty($prop['WIDTH']) )
			$Width = $prop['WIDTH'];
			else
			$Width = $this->w - $this->lMargin-$this->rMargin;
			$this->Ln(2);
			$x = $this->GetX();
			$y = $this->GetY();
			$this->SetLineWidth(0.4);
			$this->Line($x,$y,$x+$Width,$y);
			$this->SetLineWidth(0.2);
			$this->Ln(2);
		}
	}

	function CloseTag($tag)
	{
		//Closing tag
		if($tag=='B' || $tag=='I' || $tag=='U')
		$this->SetStyle($tag,false);
		if($tag=='A')
		$this->HREF='';
		if($tag=='P')
		$this->ALIGN='';
	}

	function SetStyle($tag,$enable)
	{
		//Modify style and select corresponding font
		$this->$tag+=($enable ? 1 : -1);
		$style='';
		foreach(array('B','I','U') as $s)
		if($this->$s>0)
		$style.=$s;
		$this->SetFont('',$style);
	}

	function PutLink($URL,$txt)
	{
		//Put a hyperlink
		$this->SetTextColor(0,0,255);
		$this->SetStyle('U',true);
		$this->Write(5,$txt,$URL);
		$this->SetStyle('U',false);
		$this->SetTextColor(0);
	}

	public function Footer($y = -6.2) {
		//Vai para 1.5 cm da parte inferior
		$this->SetY($y);
		//Seleciona a fonte Arial it�lico 8
		$this->SetFont('Arial','I',8);
		//Imprime o n�mero da p�gina corrente e o total de p�ginas
		$this->Cell(0, 10, 'P�gina ' . $this->PageNo() . ' de {total} - ' . strftime("%A, %d de %B de %Y", strtotime(date('Y/m/d'))) ,0,0,'C');
	}

	function Rotate($angle,$x=-1,$y=-1) {

		if($x==-1)
		$x=$this->x;
		if($y==-1)
		$y=$this->y;
		if($this->angle!=0)
		$this->_out('Q');
		$this->angle=$angle;
		if($angle!=0)

		{
			$angle*=M_PI/180;
			$c=cos($angle);
			$s=sin($angle);
			$cx=$x*$this->k;
			$cy=($this->h-$y)*$this->k;

			$this->_out(sprintf('q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
		}
	}
}
