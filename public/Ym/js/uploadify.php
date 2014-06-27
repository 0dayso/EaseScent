<?php
/*
Uploadify
Copyright (c) 2012 Reactive Apps, Ronnie Garcia
Released under the MIT License <http://www.opensource.org/licenses/mit-license.php> 
*/

// Define a destination

$sid = $_POST["usersign"];
if($sid==""){
	echo "not get usersign!";
	exit;
}
session_id($sid);
session_start();//注意此函数要在session_id之后 
$targetFolder = '/face/u_'.$_SESSION["ym_users"]["uid"]; // Relative to the root
if (!empty($_FILES)) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
	// Validate the file type
	$fileTypes = array('jpg','jpeg','gif','png'); // File extensions
	$fileParts = pathinfo($_FILES['Filedata']['name']);
	
	$targetPath = dirname($_SERVER['DOCUMENT_ROOT']) ."/Upload/Ym". $targetFolder;
	if(!is_dir($targetPath)){
		mkdir($targetPath,0777);
	}
    $fileParts['extension'] = strtolower($fileParts['extension']);
	$targetFile = rtrim($targetPath,'/') . '/'. 'myface_upload.'.$fileParts['extension'];
	
	if (in_array($fileParts['extension'],$fileTypes)) {
		//生成原图
		//move_uploaded_file($tempFile,$targetFile);
		$fileParts['extension'] = strtolower($fileParts['extension']);
		if($fileParts['extension'] == "jpg"){
			$im = imagecreatefromjpeg($_FILES['Filedata']['tmp_name']);
			}elseif($fileParts['extension'] == "png"){
			$im = imagecreatefrompng($_FILES['Filedata']['tmp_name']);
			}elseif($fileParts['extension'] == "gif"){
			$im = imagecreatefromgif($_FILES['Filedata']['tmp_name']);
			} 
			//生成缩略图
			$targetFile_lue = rtrim($targetPath,'/') . '/'. 'myface_upload.'.$fileParts['extension'];
			ResizeImage($im,293,285,$targetFile_lue);
			ImageDestroy ($im); 
            
		echo '1';
	} else {
		echo 'Invalid file type.';
	}
}


function ResizeImage($im,$maxwidth,$maxheight,$name){
	$width = imagesx($im);
	$height = imagesy($im);
	if(($maxwidth && $width > $maxwidth) || ($maxheight && $height > $maxheight)){
	if($maxwidth && $width > $maxwidth){
	$widthratio = $maxwidth/$width;
	$RESIZEWIDTH=true;
	}
	if($maxheight && $height > $maxheight){
	$heightratio = $maxheight/$height;
	$RESIZEHEIGHT=true;
	}
	if($RESIZEWIDTH && $RESIZEHEIGHT){
	if($widthratio < $heightratio){
	$ratio = $widthratio;
	}else{
	$ratio = $heightratio;
	}
	}elseif($RESIZEWIDTH){
	$ratio = $widthratio;
	}elseif($RESIZEHEIGHT){
	$ratio = $heightratio;
	}
	$newwidth = $width * $ratio;
	$newheight = $height * $ratio;
	if(function_exists("imagecopyresampled")){
	
	$newim = imagecreatetruecolor($newwidth, $newheight);
	imagecopyresampled($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
	}else{
	$newim = imagecreate($newwidth, $newheight);
	imagecopyresized($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
	}
	//ImageJpeg ($newim,$name . ".jpg");
	ImageJpeg ($newim,$name);
	ImageDestroy ($newim);
	}else{
	//ImageJpeg ($im,$name . ".jpg");
	ImageJpeg ($im,$name);
	}
} 
?>
