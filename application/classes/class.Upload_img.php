<?php

include_once('class.upload.php');
class Upload_img
{
	private $msg_erro;
	private $imagem;
	private $caminho;

	public function __construct($imagem, $caminho = './')
	{
		$this->imagem = $imagem;
		$this->caminho = $caminho;
		$this->msg_erro = '';
	}


	public function upload($borda = 0, $reflexo = 0, $icone = 0, $redimencionar = 0, $imagex = 0)
	{
		$handle = new Upload($this->imagem);

		if ($handle->uploaded) {

			$handle->file_safe_name = true;
			$nome = $this->novo_nome();
			$handle->file_new_name_body = $nome;
			if($redimencionar) {
				$handle->image_resize = true;
				$handle->image_ratio_y = true;
				$handle->image_ratio_x = true;
				if ($imagex) {
					$handle->image_x = $imagex;
				}
				else {
					$handle->image_x = 640;
				}
				$handle->image_y = 480;
			}

			if($icone) {
				$handle->image_watermark = './public/img/bordas-img.png';
				$handle->image_watermark_x = 0;
				$handle->image_watermark_y = 0;
			}

			if($borda) {
				$handle->image_bevel = 10;
				$handle->image_bevel_color1 = '#FFFFFF';
				$handle->image_bevel_color2 = '#FFFFFF';
			}

			if($reflexo) {
				$handle->image_reflection_height = '16%';
				$handle->image_reflection_space = -5;
			}

			$handle->Process($this->caminho);

			if($handle->processed) {
				$return = $handle->file_dst_name;
			}
			else {
				$this->setErro($handle->error);
				$return = false;
			}
		}
		else {
			$this->setErro('Ocorreu um erro ao fazer o upload da imagem. Tente novamente.');
			$return = false;
		}

		$handle->Clean();
		return $return;
	}

	public function novo_nome()
	{
		return substr(md5(uniqid(date('Y-m-d H:i:s'))), 0, 10);
	}

	public function valida_tipo_imagem()
	{
		if(!eregi("^image\/(jpg|pjpeg|jpeg|png|gif|bmp)$", $this->imagem['type'])) {
			$this->setErro('É permitido somente upload de imagens.');
			return false;
		}
		else {
			return true;
		}
	}

	public function setErro($msg)
	{
		$this->msg_erro = $msg;
	}

	public function getErro()
	{
		return $this->msg_erro;
	}
}