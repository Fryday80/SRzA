<?php

namespace Media\Service;
use Media\Model\Enums\EImageProcessor;

/**
 * Class ImageProcessor
 * @package Media\Service
 */
class ImageProcessor
{
	private $config;

	private $testMode = false;

	/*
	 * source data
	 */
	private $srcImage = null;
	private $srcPath;
	private $srcInfo;
	private $srcWidth;
	private $srcHeight;
	private $srcOrientation;
	private $srcAspectRatio;

	/*
	 * target data
	 */
	private $newImage = null;
	private $newOrientation;

	public function __construct($config)
	{
		$this->config = $config;
	}

	/* =========================================================
	 * Short cuts
	 * ========================================================= */

	/**
	 * Create user image smaller than limits and 2 thumbnail images <br>
	 * overwrites srcImage and saves thumbs to target paths
	 *
	 * @param $srcPath
	 * @param $targetPathSmall
	 * @param $targetPathMedium
	 */
	public function createUserImages($srcPath, $targetPathSmall, $targetPathMedium)
	{
		// get limits for profile image
		$width  = $this->config['Media_ImageProcessor']['profile_images']['x'];
		$height = $this->config['Media_ImageProcessor']['profile_images']['y'];

		if(is_object($srcPath))
			$this->loadObject($srcPath);
		else
			$this->loadPath($srcPath);

		// if src image is smaller than limits
		if ($this->srcWidth < $width && $this->srcHeight < $height) {
			$this->intern_save();
		}
		else
			//resize to limits
		{
			if ($this->srcOrientation == EImageProcessor::LANDSCAPE)
			{
				$this->intern_resize_width($width);
				$this->intern_save();
			}
			else
			{
				$this->intern_resize_height($height);
				$this->intern_save();
			}
		}
		//create the thumbs
		$this->createThumbs($srcPath, $targetPathSmall, $targetPathMedium);
	}

	/**
	 * Create thumb image
	 *
	 * @param string $item 			path/to/image
	 * @param string $targetPath1	small thumb path, null to overwrite source
	 * @param string $targetPath2	big thumb path, null to skip
	 */
	public function createThumbs($item, $targetPath1 = null, $targetPath2 = null)
	{
		if(is_object($item))
			$this->loadObject($item);
		else
			$this->loadPath($item);

		// === thumb 1
		// get size for thumb 1
		$width = $this->config['Media_ImageProcessor']['thumbs']['x1'];
		$height = $this->config['Media_ImageProcessor']['thumbs']['y1'];

		$this->intern_resize_crop($width, $height);
		$this->intern_save($targetPath1);
		// === thumb 2
		if ($targetPath2 !== null) {
			//reload src file
			$this->intern_load();

			// get size for thumb 2
			$width = $this->config['Media_ImageProcessor']['thumbs']['x2'];
			$height = $this->config['Media_ImageProcessor']['thumbs']['y2'];

			$this->intern_resize_crop($width, $height);
			$this->intern_save($targetPath2);
		}
	}

	/**
	 * Create blazon image
	 *
	 * @param string $item		 path/to/image
	 */
	public function createBlazon($item)
	{
		$width  = $this->config['Cast_ImageProcessor']['blazon']['x'];
		$height = $this->config['Cast_ImageProcessor']['blazon']['y'];

		// enhancement when/if BlazonModel was created
//		if($item instanceof BlazonModel)
//			$this->loadMediaItem($item);
//		else{}
		$this->loadPath($item);

		if ($this->srcWidth == $width && $this->srcHeight == $height){
			$this->intern_save($this->srcPath);
		} else {
			$this->intern_resize($width, $height);
			$this->intern_save();
		}
	}

	/* =========================================================
	 * API
	 * ========================================================= */

	/**
	 * Load image data from an object
	 *
	 * @param $imageObject
	 */
	public function loadObject($imageObject)
	{
		if (is_object($imageObject)) {
			if ($imageObject instanceof MediaItem) $this->loadMediaItem($imageObject);
		}
		// fall back
		else {
			$this->loadPath($imageObject);
		}
	}

	/**
	 * Load image by MediaItem
	 *
	 * @param MediaItem $item
	 */
	public function loadMediaItem(MediaItem $item)
	{
		$this->loadPath($item->fullPath);
	}

	/**
	 * Load image by path
	 *
	 * @param string $imagePath
	 *
	 * @throws \Exception
	 */
	public function loadPath($imagePath)
	{
		if (!file_exists($imagePath)) throw new \Exception("This File does not exist or path '$imagePath' is wrong");
		$this->srcPath = $imagePath;
		$this->intern_load();
	}

	/**
	 * Resize src image fitted into output image
	 *
	 * @param int  $newWidth 			 width  in px of output image
	 * @param int  $newHeight [optional] height in px of output image
	 * @param bool $keepRatio [optional] scale image with (true) or without (false) keeping original aspect ratio
	 */
	public function resize($newWidth, $newHeight = null, $keepRatio = true)
	{
		$this->intern_resize_width($newWidth, $newHeight, $keepRatio);
	}

	/**
	 * Resize src image fitted into output image
	 *
	 * @param int  $newWidth 			 width  in px of output image
	 * @param int  $newHeight [optional] height in px of output image
	 * @param bool $keepRatio [optional] scale image with (true) or without (false) keeping original aspect ratio
	 */
	public function resize_height ($newHeight, $newWidth = null, $keepRatio = true)
	{
		$this->intern_resize_height($newWidth, $newHeight, $keepRatio);
	}

	/**
	 * Resize src image fitted to output size,
	 * src aspect ratio is kept,
	 * src img is centered,
	 * parts cropped to fit
	 *
	 * @param int  $newWidth  width  in px of output image
	 * @param int  $newHeight height in px of output image
	 */
	public function resize_crop($newWidth, $newHeight)
	{
		$this->intern_resize_crop($newWidth, $newHeight);
	}

	/**
	 * Save image to $targetPath or overwrite source image
	 *
	 * @param string $targetPath string/to/save or null to overwrite source image
	 */
	public function saveImage($targetPath = null)
	{
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

	/* =========================================================
	 * Basic / common methods
	 * ========================================================= */

	/**
	 * Load image data
	 */
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

		$this->srcOrientation = ($this->srcWidth > $this->srcHeight) ? EImageProcessor::LANDSCAPE : EImageProcessor::PORTRAIT;
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

	/**
	 * Save image to $targetPath or overwrite source image
	 *
	 * @param string $targetPath string/to/save or null to overwrite source image
	 */
	private function intern_save ($targetPath = null)
	{
		if ($targetPath == null) 	 $targetPath 	 = $this->srcPath;
		if ($this->newImage == null) $this->newImage = $this->srcImage;
		if ($this->testMode) 		 $targetPath 	 = getcwd() . '/public/test.png';

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

	/**
	 * Free memory and reset objects vars
	 */
	private function intern_end()
	{
		if (is_resource($this->newImage)) imagedestroy($this->newImage );
		if (is_resource($this->newImage)) imagedestroy($this->srcImage );
		$this->newImage = null;
		$this->srcImage = null;
	}

	/* =========================================================
	 * Processing methods
	 * ========================================================= */

	// ======== resize into output size ===========
	/**
	 * Resize src image fitted into output image
	 *
	 * @param int  $newWidth 			 width  in px of output image
	 * @param int  $newHeight [optional] height in px of output image
	 * @param bool $keepRatio [optional] scale image with (true) or without (false) keeping original aspect ratio
	 */
	private function intern_resize_width($newWidth, $newHeight = null, $keepRatio = true)
	{
		if ($newHeight == null)
			$this->newImage = imagescale($this->srcImage, $newWidth);
		else
			$this->Intern_resize($newWidth, $newHeight, $keepRatio);
	}

	/**
	 * Resize src image fitted into output image
	 *
	 * @param int  $newHeight  			 height in px of output image
	 * @param int  $newWidth  [optional] width  in px of output image
	 * @param bool $keepRatio [optional] scale image with (true) or without (false) keeping original aspect ratio
	 */
	private function intern_resize_height($newHeight, $newWidth = null, $keepRatio = true)
	{
		if ($newWidth == null){
			$newWidth = $newHeight * $this->srcAspectRatio;
			$this->newImage = imagescale($this->srcImage, $newWidth);
		}
		else
			$this->Intern_resize($newWidth, $newHeight, $keepRatio);
	}

	/**
	 * Resize src image fitted into output image
	 *
	 * @param int  $newWidth 			 width  in px of output image
	 * @param int  $newHeight [optional] height in px of output image
	 * @param bool $keepRatio [optional] scale image with (true) or without (false) keeping original aspect ratio
	 */
	private function intern_resize($newWidth, $newHeight, $keepRatio = true)
	{
		$this->newOrientation = ($newWidth > $newHeight) ? EImageProcessor::LANDSCAPE : EImageProcessor::PORTRAIT;
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

			$tempImage = imagecreatetruecolor($temp_width, $temp_height);
			$transparent = imagecolortransparent($tempImage, imagecolortransparent ($this->srcImage));
			imagefill($tempImage, 0, 0, $transparent);
			imagecopyresampled(
				$tempImage,
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
				$this->newImage,			$tempImage,
				$x0, $y0,					0, 0,
				$temp_width, $temp_height, 	$temp_width, $temp_height
			);
			imagedestroy($tempImage);
		}
	}

	// ======== resize to output size and crop overleaping parts ===========
	/**
	 * Resize src image fitted to output size,
	 * src aspect ratio is kept,
	 * src img is centered,
	 * parts cropped to fit
	 *
	 * @param int  $newWidth  width  in px of output image
	 * @param int  $newHeight height in px of output image
	 */
	private function intern_resize_crop($newWidth, $newHeight)
	{
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

		$tempImage = imagecreatetruecolor($temp_width, $temp_height);
		imagecopyresampled(
			$tempImage,					$this->srcImage,
			0, 0,			0, 0,
			$temp_width, $temp_height,	$this->srcWidth, $this->srcHeight
		);

		/*
		 * Copy cropped region from temporary image into the desired GD image
		 */

		$x0 = ($temp_width - $newWidth) / 2;
		$y0 = ($temp_height - $newHeight) / 2;
		$this->newImage = imagecreatetruecolor($newWidth, $newHeight);
		imagecopy(
			$this->newImage,	$tempImage,
			0, 0,	$x0, $y0,
			$newWidth, $newHeight
		);
		imagedestroy($tempImage);
	}
}