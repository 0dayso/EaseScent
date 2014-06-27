<?php
/*	Class Watermark.class.php
 *
 *	This class is used to add watermark to 
 *	the upload picture.We could choose whether
 *	add the watermark or not.
 *	
 *	@author liuzm <liuzm@eswine.com>
 *	@date	2013-11-20
 *	@Original author	tiger <ji.xiaod@gmial.com>
 *	@Original date 2011.06.09
 *
 * */

class Watermark{
	
	public $markString = '';

	/*
	 * Method to create a watermark.
	 *	string
	 *	image
	 * */

	public $markType = 'string';
	/*
	 *	The markString locate where 
	 *	LT : left-top
	 *	LB : left-bottom
	 *	RT : right-top
	 *	RB : right-bottom
	 *	default is LT : left-top.
	 * */
	public $position = 'LT';

	public $imagePath = '';

	/*
	 * Can be 1, 2, 3, 4, 5 fr built-in fonts in latin2 encoding.
	 * Default value is 4.
	 * */
	public $fontSize = 4;

	public $marginHorizon = 2;

	public $marginVertical = 2;

	private $imageWidth;

	private $imageHeight;

	private $image;

	private $markImagePath = ''; 

	private $markImageWidth;

	private $markImageHeight;
	
	/*
	 *	Constructor
	 *
	 * */
	public function __construct( $config ){
		$this->markType = $config['markType'];
		$this->markString = $config['markString'];
		$this->fontSize = $config['fontSize'];
		$this->position = $config['position'];
		$this->markImagePath = $config['markImagePath'];	
	}

	/*
	 * Create the watermark on the picture.
	 * 
	 * */
	public function createWatermark($imagePath){
        $this->imagePath = $imagePath;
		$image = getimagesize( $this->imagePath );
        $this->imageWidth = $image[0];
        $this->imageHeight = $image[1]; 
        $image = $this->_loadImage($imagePath);

		//String watermark
		if( $this->markType == 'string' ){
			$scale = $this->_getWaterMarkScale();
			$image = $this->_insertStringWatermark( $image );
			$this->_createImage( $image );
			
		}
		//image watermark
		else if( $this->markType == 'image' ){
			$markImage = $this->_loadMarkImage();
			$image = $this->_insertImageWatermark( $image, $markImage );
			$this->_createImage( $image );
		}
        imagedestroy($image);
	}

	/*
	 *	Load the watermark image,we only support jpg image.
	 *
	 * */
	private function _loadMarkImage(){
		$image = getimagesize( $this->markImagePath );
		$this->markImageWidth = $image[0];
		$this->markImageHeight = $image[1];
		return @imagecreatefrompng( $this->markImagePath );
	}


	private function _insertImageWatermark( $image, $markImage ){
		switch ( $this->position ){
			//left top	
			case 'LT':
				imagecopy( $image, $markImage, 
							0, 0,
							0, 0,
							$this->markImageWidth, $this->markImageHeight );
				break;
			//left bottom
			case 'LB':
				imagecopy( $image, $markImage, 
							0, $this->imageHeight - $this->markImageHeight,
							0, 0,
							$this->markImageWidth, $this->markImageHeight );
				
				break;
			//right top
			case 'RT':
				imagecopy( $image, $markImage, 
							$this->imageWidth - $this->markImageWidth, 0,
							0, 0,
							$this->markImageWidth, $this->markImageHeight );
				break;
			//right bottom
			case 'RB':
				imagecopy( $image, $markImage,
							$this->imageWidth - $this->markImageWidth, $this->imageHeight - $this->markImageHeight,
							0, 0,
							$this->markImageWidth, $this->markImageHeight );	
				break;
		}
		return $image;
	}

	/*
	 *	Create image by file that given by $image
	 *
	 * */
	private function _loadImage(){
		// header('Content-Type: image/gif');
		

		//$image = getimagesize( $this->imagePath ); 	
		$type = $this->_getImageType();
		switch ( $type ){
			// image/gif
			case 'gif':
				$vitualImage = @imagecreatefromgif( $this->imagePath );
				break;	
			// image/jpg
			case 'jpg':
				$vitualImage = @imagecreatefromjpeg( $this->imagePath );
				break;
			// image/png
			case 'png': 
				$vitualImage = @imagecreatefrompng( $this->imagePath ); 
				break;

			default:
				die('Do not support the image type!');
				exit; 
		}
		return $vitualImage;
	}

	/*
	 *	Create a image by the watermark string,
	 *	so we can control the width of the string.
	 *
	 * */
	private function stringImage(){
					
	}

	/*
	 *	Get the height and width of the watermark string.
	 *	so we could locate the position of the watermark
	 *	string on the image easily.
	 *
	 *	@return {Array} scale[height, width]
	 *
	 * */
	private function _getWaterMarkScale(){
		$scale = array();
		$scale['height'] = imagefontheight($this->fontSize);
		$scale['width'] = imagefontwidth($this->fontSize) * strlen($this->markString);
		return $scale;
	}

	/*
	 *	Return the image type of the image.
	 *
	 * */
	private function _getImageType(){
		$image = getimagesize( $this->imagePath );
		$type = '';
		switch ( $image[2] ){
			// image/gif
			case 1:
				$type = 'gif';
				break;
			// image/jpg
	        case 2:
				$type = 'jpg';
			    break;
			// image/png
		    case 3:
				$type = 'png';
			    break;
			//没有相应的图片类型
			default:
				die('Do not support the image type!');
                exit;
		}
		return $type;
	}

	private function _insertStringWatermark( $image ){
	
		$textcolor = imagecolorallocate($image, 0, 0, 80);
		$markScale = $this->_getWaterMarkScale();	
			
			switch ( $this->position ){
			//left top
			case 'LT':
				imagestring( $image, $this->fontSize, 0 + $this->marginHorizon, 
												0 + $this->marginVertical,
							$this->markString, $textcolor );
				break;
				
			//left bottom
			case 'LB':
				imagestring( $image, $this->fontSize, 0 + $this->marginHorizon,
												$this->imageHeight - $markScale['height'] - $this->marginVertical,
							$this->markString, $textcolor );
				break;

			//right top
			case 'RT':
				imagestring( $image, $this->fontSize, $this->imageWidth - $markScale['width'] - $this->marginHorizon,
												0,
							$this->markString , $textcolor );
				break;

			//right bottom
			case 'RB':
				imagestring( $image, $this->fontSize, $this->imageWidth - $markScale['width'] - $this->marginHorizon,
												$this->imageHeight - $markScale['height'] - $this->marginVertical,
							$this->markString, $textcolor );
				break;

		}
		return $image;


	}
	
	/*
	 *	Cover the former image file with the new one which
	 *	added watermark.
	 *
	 *
	 * */
	private function _createImage( $image ){
		$type = $this->_getImageType();
		switch ( $type ){
			case 'gif':	
				imagegif($image, $this->imagePath); 
				break;
			case 'jpg':
				imagejpeg($image, $this->imagePath);
				break;
			case 'png':
				imagepng($image, $this->imagePath);
				break;
		}	
	}
}

?>