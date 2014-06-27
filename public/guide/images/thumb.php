<?php
	//��������ͼ�����ɰ�ָ���߿�ȱ�ѹ��������ͼ�������հ�
	function ImageResize($srcFile, $ftoW, $ftoH, $toFile, $quality = 93, $bautoresize = false)
	{
		$info = "";
		$data = getimagesize($srcFile,$info);
		switch ($data[2])
		{
			case 1: // gif
				if(!function_exists("imagecreatefromgif"))
				{
					echo "����GD2�ⲻ��ʹ��GIF��ʽ��ͼƬ,��ʹ��JPEG��PNG��ʽ�� ";
					exit();
				}
				$im = imagecreatefromgif($srcFile);
				break;
			case 2: // jpg
				if(!function_exists("imagecreatefromjpeg"))
				{
					echo "����GD2�ⲻ��ʹ��JPEG��ʽ��ͼƬ,��ʹ��������ʽ��ͼƬ�� ";
					exit();
				}
				$im = imagecreatefromjpeg($srcFile);
				break;
			case 3: // png
				$im = imagecreatefrompng($srcFile);
				break;
				case 6: // bmp
				include_once('bmpresize.php');
				$im = imagecreatefrombmp($srcFile);
				break;
		}
		$srcW = imagesx($im);
		$srcH = imagesy($im);
		////////////////  �ȱ����� /////////////////////////////////
		if ($bautoresize)
		{
			// �ȱ���С����
			// �����õȿ�
			$rh = $srcH * ($ftoW/$srcW); // �ȿ�����¸߶�Ӧ��Ϊ����
			$rw = $srcW * ($ftoH/$srcH); // �ȸ�����¿��Ӧ��Ϊ����
			if ($rw > $ftoW) // ������ȸ����ã���ȳ������õļ��ÿ�ȣ����Ϊ�����ÿ������
			{
				$ftoH = (int)($srcH * $ftoH/$srcW);
			}
			else if ($rh > $ftoH) // ������ȿ����ã��߿�ȳ������õļ��ø߶ȣ����Ϊ�����ø߶�����
			{
				$ftoW = (int)($srcW * $ftoH/$srcH);
			}
		}
		////////////////  �ȱ����� End /////////////////////////////////
		//$ftoW=185 ; //round($srcW/3);
		//$ftoH=120 ; //round($srcH/3);
		if(function_exists("imagecreatetruecolor"))
		{
			$ni = imagecreatetruecolor($ftoW,$ftoH);
			if($ni) 
			{
				imagecopyresampled($ni,$im,0,0,0,0,$ftoW,$ftoH,$srcW,$srcH);
			}
			else
			{
				$ni = imagecreate($ftoW,$ftoH);
				imagecopyresized($ni,$im,0,0,0,0,$ftoW,$ftoH,$srcW,$srcH);
			}
		}
		else
		{
			$ni = imagecreate($ftoW,$ftoH);
			imagecopyresized($ni,$im,0,0,0,0,$ftoW,$ftoH,$srcW,$srcH);
		}
		if(function_exists('imagejpeg'))
		{
			imagejpeg($ni, $toFile, $quality);
		}
		else
		{
			imagepng($ni, $toFile, $quality);
		}
		imagedestroy($ni);
		imagedestroy($im);
		return true;
	}
	
	
	// ���ò�ѹ��ͼƬ
	function ImageCutAndResize($srcFile, $ftoW, $ftoH, $toFile)
	{
		$info = "";
		$data = GetImageSize($srcFile,$info);
		switch ($data[2])
		{
			case 1: // gif
				if(!function_exists("imagecreatefromgif"))
				{
				//echo "����GD2�ⲻ��ʹ��GIF��ʽ��ͼƬ,��ʹ��JPEG��PNG��ʽ�� ";
				//exit();
					return false;
				}
				$im = ImageCreateFromGIF($srcFile);
				break;
			case 2: // jpg
				if(!function_exists("imagecreatefromjpeg"))
				{
				//echo "����GD2�ⲻ��ʹ��JPEG��ʽ��ͼƬ,��ʹ��������ʽ��ͼƬ�� ";
				//exit();
				return false;
				}
				$im = ImageCreateFromJpeg($srcFile);
				break;
			case 3: // png
				$im = ImageCreateFromPNG($srcFile);
				break;
			case 6: // bmp
				include_once('bmpresize.php');
				$im = imagecreatefrombmp($srcFile);
				break;
		}
		$srcW = ImageSX($im);
		$srcH = ImageSY($im);
		
		$src_x = 0;
		$src_y = 0;
		$src_w = $srcW;
		$src_h = $srcH;
		// ��߱ȣ��ж��ǲõ�����Ļ����ұߵ�
		if ($ftoW/$ftoH <= $srcW/$srcH) // ���е�ͼƬ���ˣ���Ҫ�õ�����
		{
			$src_w = $srcH * $ftoW/$ftoH;
			$src_x = ($srcW - $src_w) / 2;
		}
		else // ���е�ͼƬխ�ˣ���Ҫ�õ�һ��ͷβ
		{
			$src_h = $srcW * $ftoH / $ftoW;
			$src_y = ($srcH - $src_h) / 2;
		}
		if(function_exists("imagecreatetruecolor"))
		{
			$ni = ImageCreateTrueColor($ftoW,$ftoH);
			if($ni) ImageCopyResampled($ni,$im,0,0,$src_x,$src_y,$ftoW,$ftoH,$src_w,$src_h);
			else
			{
				$ni=ImageCreate($ftoW,$ftoH);
				ImageCopyResized($ni,$im,0,0,$src_x,$src_y,$ftoW,$ftoH,$src_w,$src_h);
			}
		}
		else
		{
			$ni=ImageCreate($ftoW,$ftoH);
			ImageCopyResized($ni,$im,0,0,$src_x,$src_y,$ftoW,$ftoH,$src_w,$src_h);
		}
		if(function_exists('imagejpeg')) ImageJpeg($ni,$toFile);
		else ImagePNG($ni,$toFile);
		ImageDestroy($ni);
		ImageDestroy($im);
		return true;
	}
	
	
	//��������ͼ�����ɰ�ָ���߿�ȱ�ѹ��������ͼ������пհף����Ե�ɫ��䣬ͬʱ��ͼƬ�����ڻ����м�λ��
	function ImageFillResize($srcFile, $ftoW, $ftoH, $toFile, $r = 255, $g = 255, $b = 255)
	{
		$info = "";
		$data = GetImageSize($srcFile,$info);
		switch ($data[2])
		{
			case 1: // gif
				if(!function_exists("imagecreatefromgif"))
				{
				echo "����GD2�ⲻ��ʹ��GIF��ʽ��ͼƬ,��ʹ��JPEG��PNG��ʽ�� ";
				exit();
				}
				$im = ImageCreateFromGIF($srcFile);
				break;
			case 2: // jpg
				if(!function_exists("imagecreatefromjpeg"))
				{
				echo "����GD2�ⲻ��ʹ��JPEG��ʽ��ͼƬ,��ʹ��������ʽ��ͼƬ�� ";
				exit();
				}
				$im = ImageCreateFromJpeg($srcFile);
				break;
			case 3: // png
				$im = ImageCreateFromPNG($srcFile);
				break;
			case 6: // bmp
				include_once('bmpresize.php');
				$im = imagecreatefrombmp($srcFile);
				break;
		}
		$srcW = ImageSX($im);
		$srcH = ImageSY($im);
		
		////////////////  Ѱ����ʼ�� /////////////////////////////////////
		$rtow = $srcW;
		$rtoh = $srcH;
		if (($srcW > $ftoW) || ($srcH > $ftoH))
		{
			if ($srcW/$srcH > $ftoW/$ftoH) //����
			{
				$rtow = $ftoW;
				$rtoh = floor($srcH*$rtow/$srcW);
			}
			else // ����
			{
				$rtoh = $ftoH;
				$rtow = floor($srcW*$rtoh/$srcH);
			}
		}
		$startx = floor(($ftoW - $rtow) / 2);
		$starty = floor(($ftoH - $rtoh) / 2);
		////////////////  Ѱ����ʼ�� End /////////////////////////////////
		
		if (function_exists("imagecreatetruecolor"))
		{
			$ni = ImageCreateTrueColor($ftoW, $ftoH);
			if ($ni)
			{
				// ����ɫ
				$bg = imagecolorallocate($ni, $r, $g, $b);
				imagefill($ni, 0, 0, $bg);
				ImageCopyResampled($ni, $im, $startx, $starty, 0, 0, $rtow, $rtoh, $srcW, $srcH);
			}
			else
			{
				$ni = ImageCreate($ftoW, $ftoH);
				// ����ɫ
				$bg = imagecolorallocate($ni, $r, $g, $b);
				imagefill($ni, 0, 0, $bg);
				ImageCopyResized($ni, $im, $startx, $starty, 0, 0, $rtow, $rtoh, $srcW, $srcH);
			}
		}
		else
		{
			$ni = ImageCreate($ftoW, $ftoH);
			// ����ɫ
			$bg = imagecolorallocate($ni, $r, $g, $b);
			imagefill($ni, 0, 0, $bg);
			ImageCopyResized($ni, $im, $startx, $starty, 0, 0, $rtow, $rtoh, $srcW, $srcH);
		}
		if (function_exists('imagejpeg')) ImageJpeg($ni, $toFile);
		else ImagePNG($ni,$toFile);
		ImageDestroy($ni);
		ImageDestroy($im);
		return true;
	}

?>

<?php
	//���÷���
	//�ױ����
	//ImageFillResize($srcFile, $width, $height, 'new.jpg');
	//�ü�
	//ImageCutAndResize($srcFile, $width, $height, 'cut.jpg');
	//�����
	//ImageResize($srcFile, $width, $height, $desFile, $quality, true);
	/*$quality = 93;
	
	$width	=	200;
	$height	=	200;
	ImageResize($srcFile, $width, $height, $desFile, $quality, true);
	echo "<b><font color=red>����".$i."��ͼƬ</font></b>";*/
?>