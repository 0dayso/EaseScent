<?php
class SetfaceAction extends BaseAction{
	public function index(){
        $this->assign("title",L("title_set_face"));
        $this->assign("c_face","nav-spa");

		$this->assign("usign",session_id());
		$this->assign("userinfo",$this->getUserInfo());
		$this->assign("uid",$this->getUid());
		$this->display("index");
	}
	public function save(){
		$name = time();        
        $srcX = $_POST['x'];   
        $srcY = $_POST['y'];   
        $srcWidth = $_POST['width'];    
        $srcHeight = $_POST['height'];    
        $moban = C("TMPL_PARSE_STRING"); 
        $faceaddr = "/Upload/Ym/face";
        $extend = strtolower($_POST["extend"]);    
      /*	if($this->mkdirs($imagePath)){  
           $src = $_SERVER['DOCUMENT_ROOT'].$moban["__IMG__"]."/u_".$_SESSION["ym_users"]["uid"].'/myface.jpg';
           $dst = $imagePath.'d_'.$srcFile;
            $a = $this->makeThumb($src,$dst,max(345,min(345,$srcWidth)),217,0,0,$srcX,$srcY,$srcWidth,$srcHeight);
            if($a){ 
                $s_dst = $imagePath.'s_'.$srcFile;
                $b = $this->makeThumb($src,$s_dst,158,100,0,0,$srcX,$srcY,$srcWidth,$srcHeight);                                                
            }    
        }        
        if(!empty($a) && !empty($b)){   
            echo basename($b);
        }else{   
            echo 1000011;      
        }  */
        $src = dirname($_SERVER['DOCUMENT_ROOT']).$faceaddr."/u_".$_SESSION["ym_users"]["uid"].'/myface_upload.'.$extend;
      $imagePath =  dirname($_SERVER['DOCUMENT_ROOT']).$faceaddr."/u_".$_SESSION["ym_users"]["uid"].'/';
       $dst = $imagePath.'d_myface.'.$extend;  
       $a = $this->makeThumb($src,$dst,207,200,0,0,$srcX,$srcY-10,$srcWidth,$srcHeight); 
      if($a){
       		$s_dst = $imagePath.'s_myface.'.$extend;  
       		$b = $this->makeThumb($src,$s_dst,85,80,0,0,$srcX,$srcY-10,$srcWidth,$srcHeight);
       }
       $userinfoModel = D("UsersInfo");
       $data["face_type"] = $extend;
       $userinfoModel->where("id=".$this->getUid())->save($data);
	   echo json_encode(array("status"=>1,"message"=>L("face_save_success")));
	}
	public function makeThumb($srcfile,$dstfile,$thumbwidth,$thumbheight,$maxthumbwidth=0,$maxthumbheight=0,$src_x=0,$src_y=0,$src_w=0,$src_h=0) {
		if (!is_file($srcfile)) {
			return '';
		}
		$tow = (int) $thumbwidth;
		$toh = (int) $thumbheight;
		if($tow < 30) {
			$tow = 30;
		}
		if($toh < 30) {
			$toh = 30;
		}

		$make_max = 0;
		$maxtow = (int) $maxthumbwidth;
		$maxtoh = (int) $maxthumbheight;
		if($maxtow >= 300 && $maxtoh >= 300){
			$make_max = 1;
		}
		$im = '';
		if($data = getimagesize($srcfile)) {
			if($data[2] == 1) {
				$make_max = 0;
				if(function_exists("imagecreatefromgif")) {
					$im = imagecreatefromgif($srcfile);
				}
			} elseif($data[2] == 2) {
				if(function_exists("imagecreatefromjpeg")) {
					$im = imagecreatefromjpeg($srcfile);
				}
			} elseif($data[2] == 3) {
				if(function_exists("imagecreatefrompng")) {
					$im = imagecreatefrompng($srcfile);
				}
			}
		}
		if(!$im) return '';

		$srcw = ($src_w ? $src_w : imagesx($im));
		$srch = ($src_h ? $src_h : imagesy($im));

		$towh = $tow/$toh;
		$srcwh = $srcw/$srch;
		if($towh <= $srcwh){
			$ftow = $tow;
			$ftoh = round($ftow*($srch/$srcw),2);
		}
		else{
			$ftoh = $toh;
			$ftow = round($ftoh*($srcw/$srch),2);
		}
		
		if($make_max){
			$maxtowh = $maxtow/$maxtoh;
			if($maxtowh <= $srcwh){
				$fmaxtow = $maxtow;
				$fmaxtoh = round($fmaxtow*($srch/$srcw),2);
			}
			else{
				$fmaxtoh = $maxtoh;
				$fmaxtow = round($fmaxtoh*($srcw/$srch),2);
			}

			if($srcw <= $maxtow && $srch <= $maxtoh){
				$make_max = 0;    	
			}
		}
		$maxni = '';
		//if($srcw >= $tow || $srch >= $toh) {
			if(function_exists("imagecreatetruecolor") && function_exists("imagecopyresampled") && ($ni = imagecreatetruecolor($ftow, $ftoh))) {
				imagecopyresampled($ni, $im, 0, 0, $src_x, $src_y, $ftow, $ftoh, $srcw, $srch);
							if($make_max && ($maxni = imagecreatetruecolor($fmaxtow, $fmaxtoh))) {
					imagecopyresampled($maxni, $im, 0, 0, $src_x, $src_y, $fmaxtow, $fmaxtoh, $srcw, $srch);
				}
			} elseif(function_exists("imagecreate") && function_exists("imagecopyresized") && ($ni = imagecreate($ftow, $ftoh))) {
				imagecopyresized($ni, $im, 0, 0, $src_x, $src_y, $ftow, $ftoh, $srcw, $srch);
							if($make_max && ($maxni = imagecreate($fmaxtow, $fmaxtoh))) {
					imagecopyresized($maxni, $im, 0, 0, $src_x, $src_y, $fmaxtow, $fmaxtoh, $srcw, $srch);
				}
			} else {
				return '';
			}
			if(function_exists('imagejpeg')) {
				imagejpeg($ni, $dstfile, 100);
							if($make_max && $maxni) {
					imagejpeg($maxni, $srcfile, 100);
				}
			} elseif(function_exists('imagepng')) {
				imagepng($ni, $dstfile);
							if($make_max && $maxni) {
					imagepng($maxni, $srcfile);
				}
			}
			imagedestroy($ni);
			if($make_max && $maxni) {
				imagedestroy($maxni);
			}
		//}
		imagedestroy($im);
		if(!is_file($dstfile)) {
			return '';
		} else {
			return $dstfile;
		}
	}	
}
