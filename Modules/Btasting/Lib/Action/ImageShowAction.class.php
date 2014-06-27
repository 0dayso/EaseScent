<?php

/**
 * 头像生成Action
 *
 * @author mengfk@eswine.com
 * @since 20130401
 */

class ImageShowAction extends Action {
	
	public function avatar() {
		$w = intval($_GET['w']);
		$h = intval($_GET['h']);
		$id = intval($_GET['avatar']);
		$avatar = M('avatar')->where(array('id' => $id))->find();
		$this->_showPic(C('AVATAR_PATH').$avatar['path'], $w, $h);
    }

    public function rich() {
		$w = intval($_GET['w']);
		$h = intval($_GET['h']);
		$id = intval($_GET['rich']);
		$adv = M('advimage')->where(array('id' => $id))->find();
		$this->_showPic(C('ADVIMG_PATH').$adv['path'], $w, $h);
    }

	private function _showPic($filepath, $w = 180, $h = 180) {
        $path = $filepath;
        if(!is_file($path)) {
            die('Image does not exists.');
        }
        $this->_createImage($path, $w, $h);
    }

    private function _createImage($im, $width='', $height='', $quality=95){
	    $file = basename($im);
	    $tempArr = explode(".", $file);
	    $fileExt = array_pop($tempArr);
	    $fileExt = trim($fileExt);
        $ext = strtolower($fileExt);

	    if(!in_array($ext,array('gif','jpg','jpeg','png'))){
            return false;
	    }
        
        //取得当前图片信息
	    $picSize=getimagesize($im);
	    $imgType = array(1=>'gif', 2=>'jpeg', 3=>'png');
        $iwidth = $picSize[0];
	    $iheight = $picSize[1];
    	$itype    = $imgType[$picSize[2]];
	    $funcCreate = "imagecreatefrom".$itype;
	    if (!function_exists ($funcCreate) && !function_exists('imagecreatetruecolor') && !function_exists('imagecopyresampled')) {
	        return '';
        }
        if($width == '' && $height != '') {
            //等比缩，以高为基准
            $x = 0;
            $y = 0;
            $h = $iheight;
            $w = $iwidth;
            if($height > $iheight) {
                $width = $iwidth;
                $height = $iheight;
            } else {
                $width = round($height/$iheight) * $iwidth;
            }
        } elseif($height == '' && $width != '') {
            //等比缩，以宽为基准
            $x = 0;
            $y = 0;
            $h = $iheight;
            $w = $iwidth;
            if($width > $iwidth) {
                $width = $iwidth;
                $height = $iheight;
            } else {
                $height = round(($width/$iwidth) *$iheight);
            }
        } else {
	        //智能生成缩略图宽高
	        $ws = $iwidth/$width;
	        $hs = $iheight/$height;
	        if($ws>=1 && $hs>=1){
                if($ws>$hs){
		            $w = round($width * $hs);
		            $h = $iheight;
		            $x = round(($iwidth - $w)/2);
		            $y = 0;
                } else {
                    $w = $iwidth;
		            $h = round($height * $ws);
		            $x = 0;
		            $y = round(($iheight - $h)/2);
                }
    	    } elseif($ws<1 && $hs<1) {
                $w = $iwidth;
                $h = $iheight;
                $x = 0;
                $y = 0;
                $width = $iwidth;
                $height = $iheight;
            } elseif($ws>1 && $hs<1) {
                $w = $width;
                $h = $iheight;
                $height = $iheight;
                $x = ($iwidth - $w)/2;
                $y = 0;
	        } else {
                $w = $iwidth;
                $h = $height;
                $width = $iwidth;
                $x = 0;
                $y = ($iheight - $h)/2;
	        }
        }
	    //获取图像
	    $img = @$funcCreate($im);
        //新建图像
	    $thumb = imagecreatetruecolor($width, $height);
	    //复制图像
	    imagecopyresampled($thumb, $img, 0, 0, $x ,$y, $width, $height, $w, $h);
	    $funcOut = "image" . $itype;
        $funcOut($thumb);
    }
}
