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
	private $srcAspectRatio;
	private $tempImage;

	/*
	 * Test mode
	 */
	private $testMode = false;
	private $testPath = 'set in constructor due getcwd()';

	public function __construct($config)
	{
		$this->config = $config;

		if ($this->testMode)
			$this->testPath = getcwd() . '/public/test.png';
	}

	// ======================================================== short cuts

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
	}

	public function createBlazon($item, $targetPath = null)
	{
		// enhancement when/if BlazonModel was created
//		if($item instanceof BlazonModel)
//			$this->loadMediaItem($item);
//		else{}
		$this->loadPath($item);

		if ($this->srcWidth == 100 && $this->srcHeight == 100){
			$this->newImage = $this->srcImage;
		} else {
			$width  = $this->config['Cast_ImageProcessor']['blazon']['x'];
			$height = $this->config['Cast_ImageProcessor']['blazon']['y'];
			$this->intern_resize($width, $height);
		}

		$this->intern_save($targetPath);
	}

	// ======================================================== api

	public function loadPath($imagePath)
	{
		if (!file_exists($imagePath)) throw new \Exception("This File does not exist or path '$imagePath' is wrong");
		$this->srcPath = $imagePath;
		$this->intern_load();
	}

	public function loadMediaItem(MediaItem $item)
	{
		$this->loadPath($item->fullPath);
	}

	public function resize($newWidth, $newHeight = null, $keepRatio = true)
	{
		$this->intern_resize($newWidth, $newHeight, $keepRatio);
	}

	public function resize_crop($newWidth, $newHeight) {
		$this->intern_resize_crop($newWidth, $newHeight);
	}

	public function saveImage($targetPath = null) {
		$this->intern_save($targetPath);
	}

	/**
	 * Toggle or set test mode
	 *
	 * @param bool   $flag     [optional] true turns mode on, false turns mode off, nothing toggles on <->off
	 * @param string $testPath [optional]
	 */
	public function testMode($flag = null, $testPath = null)
	{
		if ($testPath !== null)
			$this->testPath = $testPath;
		if ($flag == null) $this->testMode = ($this->testMode) ? false : true;
		else $this->testMode = $flag;
		var_dump('Image Processor test mode set on ' . $this->testMode);
	}

	// ======================================================== basic methods

	private function intern_load()
	{
		$this->srcInfo = pathinfo($this->srcPath);
		switch ($this->srcInfo['extension']){
			case 'png':
		$this->srcImage  = imagecreatefrompng($this->srcPath);
				break;
			case 'jpg':
			case 'jpeg':
		$this->srcImage  = imagecreatefromjpeg($this->srcPath);
				break;
			case 'gif':
		$this->srcImage  = imagecreatefromgif($this->srcPath);
				break;
		}

		list($this->srcWidth, $this->srcHeight) = getimagesize($this->srcPath);

		$this->srcOrientation = ($this->srcWidth > $this->srcHeight) ? 'landscape' : 'portrait';
		$this->srcAspectRatio = $this->srcWidth / $this->srcHeight;

		switch ($this->srcInfo['extension']){
			case 'png':
			case 'gif':
				imagealphablending($this->srcImage, false);
				imagesavealpha($this->srcImage, true);
				$transparent = imagecolortransparent($this->srcImage);// get transparent Color
				imagecolorset($this->srcImage,$transparent,255, 235, 215); // SET NEW COLOR = bg color
				imagecolorallocatealpha($this->srcImage, 255, 235, 215, 127);
				break;
			default:
				break;
		}
	}

	private function intern_save ($targetPath = null)
	{
		if ($targetPath == null) 	 $targetPath 	 = $this->srcPath;
		if ($this->newImage == null) $this->newImage = $this->srcImage;
		if ($this->testMode) 		 $targetPath 	 = $this->testPath;

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
		$this->intern_end();
	}

	private function intern_end()
	{
		if (is_resource($this->newImage)) imagedestroy($this->newImage );
		if (is_resource($this->newImage)) imagedestroy($this->srcImage );
		if (is_resource($this->newImage)) imagedestroy($this->tempImage);
		$this->newImage = null;
		$this->srcImage = null;
		$this->tempImage = null;
	}

	// ======================================================== processing methods

	private function intern_resize($newWidth, $newHeight = null, $keepRatio = true) {
		if ($newHeight == null)
			$this->newImage = imagescale($this->srcImage, $newWidth);
		else {
			$this->newOrientation = ($newWidth > $newHeight) ? 'landscape' : 'portrait';
			if ($keepRatio !== true)
				$this->newImage = imagescale($this->srcImage, $newWidth, $newHeight);
			else {
				/*
			 * Crop-to-fit PHP-GD
			 * http://salman-w.blogspot.com/2009/04/crop-to-fit-image-using-aspphp.html
			 *
			 * Resize and center crop an arbitrary size image to fixed width and height
			 * e.g. convert a large portrait/landscape image to a small square thumbnail
			 *
			 * Modified by Fry
				 */
				$desired_aspect_ratio = $newWidth / $newHeight;

				if ($this->srcAspectRatio < $desired_aspect_ratio) {
					/*
					 * Triggered when source image is taller
					 */
					$temp_height = $newHeight;
					$temp_width = ( int )($newHeight * $this->srcAspectRatio);
				} else {
					/*
					 * Triggered otherwise (i.e. source image is similar or wider)
					 */
					$temp_width = $newWidth;
					$temp_height = ( int )($newWidth / $this->srcAspectRatio);
				}

				/*
				 * Resize the image into a temporary GD image
				 */

				$this->tempImage = imagecreatetruecolor($temp_width, $temp_height);
				$transparent = imagecolortransparent($this->tempImage, imagecolortransparent ($this->srcImage));
				imagefill($this->tempImage, 0, 0, $transparent);
				imagecopyresampled(
					$this->tempImage,
					$this->srcImage,
					0, 0,
					0, 0,
					$temp_width, $temp_height,
					$this->srcWidth, $this->srcHeight
				);

				$x0 = ($newWidth  - $temp_width ) / 2;
				$y0 = ($newHeight - $temp_height) / 2;

				$this->newImage = imagecreatetruecolor($newWidth, $newHeight);
				$transparent = imagecolortransparent($this->newImage, imagecolorallocatealpha($this->newImage, 255, 235, 215, 127));
				imagefill($this->newImage, 0, 0, $transparent);
				imagecopyresampled(
					$this->newImage, 									$this->tempImage,
					$x0, $y0,											0, 0,
					$temp_width, $temp_height, 	$temp_width, $temp_height
				);
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
	 *
	 * Modified by Fry
		 */
		$desired_aspect_ratio = $newWidth / $newHeight;

		if ($this->srcAspectRatio > $desired_aspect_ratio) {
			/*
			 * Triggered when source image is wider
			 */
			$temp_height = $newHeight;
			$temp_width = ( int ) ($newHeight * $this->srcAspectRatio);
		} else {
			/*
			 * Triggered otherwise (i.e. source image is similar or taller)
			 */
			$temp_width = $newWidth;
			$temp_height = ( int ) ($newWidth / $this->srcAspectRatio);
		}

		/*
		 * Resize the image into a temporary GD image
		 */

		$this->tempImage = imagecreatetruecolor($temp_width, $temp_height);
		imagecopyresampled(
			$this->tempImage,
			$this->srcImage,
			0, 0,
			0, 0,
			$temp_width, $temp_height,
			$this->srcWidth, $this->srcHeight
		);

		/*
		 * Copy cropped region from temporary image into the desired GD image
		 */

		$x0 = ($temp_width - $newWidth) / 2;
		$y0 = ($temp_height - $newHeight) / 2;
		$this->newImage = imagecreatetruecolor($newWidth, $newHeight);
		imagecopy(
			$this->newImage,
			$this->tempImage,
			0, 0,
			$x0, $y0,
			$newWidth, $newHeight
		);
	}
}