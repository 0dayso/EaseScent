<?php
	//生成缩略图，生成按指定高宽等比压缩的缩略图，不留空白
	function ImageResize($srcFile, $ftoW, $ftoH, $toFile, $quality = 93, $bautoresize = false)
	{
		$info = "";
		$data = getimagesize($srcFile,$info);
		switch ($data[2])
		{
			case 1: // gif
				if(!function_exists("imagecreatefromgif"))
				{
					echo "您的GD2库不能使用GIF格式的图片,请使用JPEG或PNG格式！ ";
					exit();
				}
				$im = imagecreatefromgif($srcFile);
				break;
			case 2: // jpg
				if(!function_exists("imagecreatefromjpeg"))
				{
					echo "您的GD2库不能使用JPEG格式的图片,请使用其他格式的图片！ ";
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
		////////////////  等比设置 /////////////////////////////////
		if ($bautoresize)
		{
			// 等比缩小设置
			// 先设置等宽
			$rh = $srcH * ($ftoW/$srcW); // 等宽情况下高度应该为多少
			$rw = $srcW * ($ftoH/$srcH); // 等高情况下宽度应该为多少
			if ($rw > $ftoW) // 如果按等高设置，宽度超过设置的剪裁宽度，则改为按剪裁宽度设置
			{
				$ftoH = (int)($srcH * $ftoH/$srcW);
			}
			else if ($rh > $ftoH) // 如果按等宽设置，高宽度超过设置的剪裁高度，则改为按剪裁高度设置
			{
				$ftoW = (int)($srcW * $ftoH/$srcH);
			}
		}
		////////////////  等比设置 End /////////////////////////////////
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
	
	
	// 剪裁并压缩图片
	function ImageCutAndResize($srcFile, $ftoW, $ftoH, $toFile)
	{
		$info = "";
		$data = GetImageSize($srcFile,$info);
		switch ($data[2])
		{
			case 1: // gif
				if(!function_exists("imagecreatefromgif"))
				{
				//echo "您的GD2库不能使用GIF格式的图片,请使用JPEG或PNG格式！ ";
				//exit();
					return false;
				}
				$im = ImageCreateFromGIF($srcFile);
				break;
			case 2: // jpg
				if(!function_exists("imagecreatefromjpeg"))
				{
				//echo "您的GD2库不能使用JPEG格式的图片,请使用其他格式的图片！ ";
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
		// 宽高比，判断是裁掉下面的还是右边的
		if ($ftoW/$ftoH <= $srcW/$srcH) // 现有的图片宽了，需要裁掉两边
		{
			$src_w = $srcH * $ftoW/$ftoH;
			$src_x = ($srcW - $src_w) / 2;
		}
		else // 现有的图片窄了，需要裁掉一点头尾
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
	
	
	//生成缩略图，生成按指定高宽等比压缩的缩略图，如果有空白，则以底色填充，同时将图片生成在画布中间位置
	function ImageFillResize($srcFile, $ftoW, $ftoH, $toFile, $r = 255, $g = 255, $b = 255)
	{
		$info = "";
		$data = GetImageSize($srcFile,$info);
		switch ($data[2])
		{
			case 1: // gif
				if(!function_exists("imagecreatefromgif"))
				{
				echo "您的GD2库不能使用GIF格式的图片,请使用JPEG或PNG格式！ ";
				exit();
				}
				$im = ImageCreateFromGIF($srcFile);
				break;
			case 2: // jpg
				if(!function_exists("imagecreatefromjpeg"))
				{
				echo "您的GD2库不能使用JPEG格式的图片,请使用其他格式的图片！ ";
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
		
		////////////////  寻找起始点 /////////////////////////////////////
		$rtow = $srcW;
		$rtoh = $srcH;
		if (($srcW > $ftoW) || ($srcH > $ftoH))
		{
			if ($srcW/$srcH > $ftoW/$ftoH) //超宽
			{
				$rtow = $ftoW;
				$rtoh = floor($srcH*$rtow/$srcW);
			}
			else // 超高
			{
				$rtoh = $ftoH;
				$rtow = floor($srcW*$rtoh/$srcH);
			}
		}
		$startx = floor(($ftoW - $rtow) / 2);
		$starty = floor(($ftoH - $rtoh) / 2);
		////////////////  寻找起始点 End /////////////////////////////////
		
		if (function_exists("imagecreatetruecolor"))
		{
			$ni = ImageCreateTrueColor($ftoW, $ftoH);
			if ($ni)
			{
				// 填充底色
				$bg = imagecolorallocate($ni, $r, $g, $b);
				imagefill($ni, 0, 0, $bg);
				ImageCopyResampled($ni, $im, $startx, $starty, 0, 0, $rtow, $rtoh, $srcW, $srcH);
			}
			else
			{
				$ni = ImageCreate($ftoW, $ftoH);
				// 填充底色
				$bg = imagecolorallocate($ni, $r, $g, $b);
				imagefill($ni, 0, 0, $bg);
				ImageCopyResized($ni, $im, $startx, $starty, 0, 0, $rtow, $rtoh, $srcW, $srcH);
			}
		}
		else
		{
			$ni = ImageCreate($ftoW, $ftoH);
			// 填充底色
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
	//调用方法
	//白边填充
	//ImageFillResize($srcFile, $width, $height, 'new.jpg');
	//裁剪
	//ImageCutAndResize($srcFile, $width, $height, 'cut.jpg');
	//无填充
	//ImageResize($srcFile, $width, $height, $desFile, $quality, true);
	/*$quality = 93;
	
	$width	=	200;
	$height	=	200;
	ImageResize($srcFile, $width, $height, $desFile, $quality, true);
	echo "<b><font color=red>处理".$i."张图片</font></b>";*/
?>