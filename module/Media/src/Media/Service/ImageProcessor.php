<?php

namespace Media\Service;


use vakata\database\Exception;

class ImageProcessor
{
	private $config;

	private $srcImage = null;
	private $srcPath;
	private $srcInfo;
	private $srcWidth;
	private $srcHeight;
	private $srcOrientation;

	private $newImage = null;
	private $newOrientation;

	public function __construct($config)
	{
		$this->config = $config;
	}



	public function createThumb($item, $targetPath = null)
	{
		if($item instanceof MediaItem)
			$this->loadMediaItem($item);
		else
			$this->loadPath($item);
		$width = $this->config['Media_ImageProcessor']['thumbs']['x1'];
		$height = $this->config['Media_ImageProcessor']['thumbs']['y1'];
		$this->intern_resize_crop($width, $height);
		$this->intern_save($targetPath);
		$width = $this->config['Media_ImageProcessor']['thumbs']['x2'];
		$height = $this->config['Media_ImageProcessor']['thumbs']['y2'];
		$this->intern_resize_crop($width, $height);
		$this->intern_save($targetPath);
		$this->intern_end();
	}

	public function createBlazon($item, $targetPath = null)
	{
		// enhancement when/if BlazonModel was created
//		if($item instanceof BlazonModel)
//			$this->loadMediaItem($item);
//		else
			$this->loadPath($item);
		$width  = $this->config['Cast_ImageProcessor']['blazon']['x'];
		$height = $this->config['Cast_ImageProcessor']['blazon']['y'];
		$this->intern_resize_crop($width, $height);
		$this->intern_save($targetPath);
		$this->intern_end();
	}


public function test (){
		$return = $this->newImage;
		bdump('return');
		bdump($return);
		return $return;
}

	public function loadPath($imagePath)
	{
		$this->temp = bdump(getcwd()) . '/public/test.png';
		if (!file_exists($imagePath)) throw new \Exception("This File does not exist or path '$imagePath' is wrong");
		$this->srcPath = $imagePath;
		$this->intern_load();
	}

	public function loadMediaItem(MediaItem $item)
	{
		$this->loadPath($item->fullPath);
	}

	public function resize($newWidth, $newHeight = null, $keepRatio = true) {
		$this->intern_resize($newWidth, $newHeight, $keepRatio);
	}

	public function resize_crop($newWidth, $newHeight) {
		$this->intern_resize_crop($newWidth, $newHeight);
	}

	public function saveImage($targetPath = null) {
		$this->intern_save($targetPath);
		$this->intern_end();
	}


	private function intern_load()
	{
		$srcImg = file_get_contents($this->srcPath);
		$this->srcImage  = imagecreatefromstring($srcImg);
		$this->srcWidth  = imagesx($this->srcImage);
		$this->srcHeight = imagesy($this->srcImage);

		$this->srcInfo = pathinfo($this->srcPath);

		$this->srcOrientation = ($this->srcWidth > $this->srcHeight) ? 'landscape' : 'portrait';
	}

	private function intern_save ($targetPath = null)
	{
		if ($targetPath == null) $targetPath = $this->srcPath;
		if ($this->newImage == null) $this->newImage = $this->srcImage;

		//cleanfix
		$targetPath = $this->temp;

		switch($this->srcInfo['extension']) {
			case 'jpg':
			case 'jpeg':
				imagejpeg ( $this->newImage, $targetPath);
				break;
			case 'png':
				imagepng  ( $this->newImage, $targetPath);
				break;
			case 'gif':
				imagegif  ( $this->newImage, $targetPath);
		}
	}

	private function intern_resize($newWidth, $newHeight = null, $keepRatio = true) {
		if ($newHeight == null)
			$this->newImage = imagescale($this->srcImage, $newWidth);
		else {
			$this->newOrientation = ($newWidth > $newHeight) ? 'landscape' : 'portrait';
			if ($keepRatio !== true)
				$this->newImage = imagescale($this->srcImage, $newWidth, $newHeight);
			else
			{
				$this->newImage = imagecreatetruecolor($newWidth, $newHeight);
				$transparent = imagecolortransparent($this->newImage, imagecolorallocatealpha($this->newImage, 255, 255, 255, 127));
				imagefill($this->newImage, 0, 0, $transparent);

				$startWidth = $startHeight = 0;

				// @salt einfach ignorieren, da müssen noch fallunterscheidungen rein
				//

//				$srcScale = $this->srcWidth/$src
//				if ($this->srcOrientation == $this->newOrientation) {
//					if ()
//				}
//				else {
//
//				}




				if ($this->srcOrientation == $this->newOrientation) {
					$this->srcImage = imagescale($this->srcImage, $newWidth);
					ImageAlphaBlending($this->srcImage, true);

					if ($this->srcWidth > $this->srcHeight) {
						$differenceH = ($newHeight - $this->srcHeight);
						$startHeight = $differenceH / 2;
					} else {
						$differenceW = ($newWidth - $this->srcWidth);
						$startWidth = $differenceW / 2;
					}

					imagecopyresized(
						$this->newImage, $this->srcImage,
						$startWidth, $startHeight, 0, 0,
						$newWidth, $newHeight, $this->srcWidth + $differenceH, $this->srcHeight + $differenceH
					);
				}
			}
		}
	}

	private function intern_resize_crop($newWidth, $newHeight) {
/*
 * Crop-to-fit PHP-GD
 * http://salman-w.blogspot.com/2009/04/crop-to-fit-image-using-aspphp.html
 *
 * Resize and center crop an arbitrary size image to fixed width and height
 * e.g. convert a large portrait/landscape image to a small square thumbnail
 */

define('DESIRED_IMAGE_WIDTH', $newWidth);
define('DESIRED_IMAGE_HEIGHT', $newHeight);

$source_path = $_FILES['Image1']['tmp_name'];

/*
 * Add file validation code here
 */

list($source_width, $source_height, $source_type) = getimagesize($source_path);

switch ($source_type) {
	case IMAGETYPE_GIF:
		$source_gdim = imagecreatefromgif($source_path);
		break;
	case IMAGETYPE_JPEG:
		$source_gdim = imagecreatefromjpeg($source_path);
		break;
	case IMAGETYPE_PNG:
		$source_gdim = imagecreatefrompng($source_path);
		break;
}

$source_aspect_ratio = $source_width / $source_height;
$desired_aspect_ratio = DESIRED_IMAGE_WIDTH / DESIRED_IMAGE_HEIGHT;

if ($source_aspect_ratio > $desired_aspect_ratio) {
	/*
	 * Triggered when source image is wider
	 */
	$temp_height = DESIRED_IMAGE_HEIGHT;
	$temp_width = ( int ) (DESIRED_IMAGE_HEIGHT * $source_aspect_ratio);
} else {
	/*
	 * Triggered otherwise (i.e. source image is similar or taller)
	 */
	$temp_width = DESIRED_IMAGE_WIDTH;
	$temp_height = ( int ) (DESIRED_IMAGE_WIDTH / $source_aspect_ratio);
}

/*
 * Resize the image into a temporary GD image
 */

$temp_gdim = imagecreatetruecolor($temp_width, $temp_height);
imagecopyresampled(
	$temp_gdim,
	$source_gdim,
	0, 0,
	0, 0,
	$temp_width, $temp_height,
	$source_width, $source_height
);

/*
 * Copy cropped region from temporary image into the desired GD image
 */

$x0 = ($temp_width - DESIRED_IMAGE_WIDTH) / 2;
$y0 = ($temp_height - DESIRED_IMAGE_HEIGHT) / 2;
$desired_gdim = imagecreatetruecolor(DESIRED_IMAGE_WIDTH, DESIRED_IMAGE_HEIGHT);
imagecopy(
	$desired_gdim,
	$temp_gdim,
	0, 0,
	$x0, $y0,
	DESIRED_IMAGE_WIDTH, DESIRED_IMAGE_HEIGHT
);

/*
 * Render the image
 * Alternatively, you can save the image in file-system or database
 */

header('Content-type: image/jpeg');
imagejpeg($desired_gdim);

/*
 * Add clean-up code here
 */


		$startWidth = $startHeight = 0;
		if ($newWidth < $newHeight) {
			$resized = imagescale($this->srcImage, $newWidth);
			$y = imagesy($resized);
			$startHeight = ($y - $newHeight)/2;
		} else {
			$scale = $this->srcWidth/$this->srcHeight;
			$resized = imagescale($this->srcImage, ($newHeight*$scale));
			$x = imagesx($resized);
			$startWidth = ($x - $newWidth)/2;
		}
		$this->newImage = imagecreatetruecolor($newWidth, $newHeight);

		imagecopyresized($this->newImage, $resized, 0,0, $startWidth, $startHeight, $newWidth, $newHeight, $this->srcWidth, $this->srcHeight);
	}

	private function intern_end()
	{
		imagedestroy($this->newImage);
		imagedestroy($this->srcImage);
		$this->newImage = null;
		$this->srcImage = null;
	}
}