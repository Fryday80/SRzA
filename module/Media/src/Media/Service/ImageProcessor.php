<?php

namespace Media\Service;

use Media\Model\MediaItem;
use Media\Utility\Pathfinder;
use Media\Model\Enums\EImageProcessor;

/**
 * Class ImageProcessor
 * @package Media\Service
 */
class ImageProcessor
{
	// defaults    set in constructor
	private $root;
	private $dataRootPath;

	// configuration
	private $config;
	/** @var array string[] */
	private $possibleExtensions = array (
		'jpg', 'jpeg',
		'gif',
		'png',
	);

	// given item
	private $item;

	// source data
	public $meta; // set in constructor

	private $srcImage = null;
	/** @var  string absolute path */
	private $srcPath;
	private $srcInfo;
	private $srcWidth;
	private $srcHeight;
	private $srcSize;
	private $srcOrientation;
	private $srcAspectRatio;

	// target data
	private $newImage = null;
	private $newOrientation;

	// php/global limits
	private $maxFileSize;


	public function __construct($config)
	{
		$this->config = $config;
		$this->root = Pathfinder::cleanPath( $this->config['ImageProcessor']['root'] );
		$this->dataRootPath = Pathfinder::cleanPath( $this->config['ImageProcessor']['dataRoot'] );
		$this->meta = array (
			'resource' 	  => &$this->srcImage,
			'path'     	  => &$this->srcPath,
			'info' 	   	  => &$this->srcInfo,
			'width'    	  => &$this->srcWidth,
			'height'   	  => &$this->srcHeight,
			'size'     	  => &$this->srcSize,
			'orientation' => &$this->srcOrientation,
			'ratio' 	  => &$this->srcAspectRatio,
		);
	}

	/* =========================================================
	 * API
	 * ========================================================= */

	/**
	 * Set Data Root Path, overrides config
	 * @param $dataRootPath
	 */
	public function setDataRootPath($dataRootPath)
	{
		$this->dataRootPath = $dataRootPath;
	}

	/**
	 * Load media item to process
	 *
	 * @param MediaItem $item
	 */
	public function load( MediaItem $item )
	{
		// save original given data for multiple processing or post processing
		$this->item = $item;
		// load item
		$this->loadFromMediaItem($item);
	}

	public function reset(){
		$this->intern_load();
	}

	/**
	 * Resize src image fitted into output image
	 *
	 * @param int  $newWidth 			 width  in px of output image
	 * @param int  $newHeight [optional] height in px of output image
	 * @param bool $keepRatio [optional] scale image with (true) or without (false) keeping original aspect ratio
	 */
	public function resize_width($newWidth, $newHeight = null, $keepRatio = true)
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
		$this->intern_resize_height($newHeight, $newWidth, $keepRatio);
	}

	/**
	 * Resize Image to a square image <br/>
	 * short side centered in new image
	 *
	 * @param $side
	 */
	public function resize_square($side)
	{
		if ($this->srcOrientation == EImageProcessor::LANDSCAPE)
			$this->intern_resize_width($side, $side);
		else
			$this->intern_resize_height($side, $side);
	}

	/**
	 * Resize Images long side to $maxSideSize keeping ratio
	 *
	 * @param $maxSideSize
	 */
	public function resizeToMaxSide($maxSideSize)
	{
		if ($this->srcOrientation == EImageProcessor::LANDSCAPE)
			$this->intern_resize_width($maxSideSize);
		else
			$this->intern_resize_height($maxSideSize);
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
	 * @param int   $percentage value < 0 minifies value > 0 enlarges image
	 */
	public function resizeDiscSize($percentage = -10)
	{
		$this->intern_resizeDiscSize($percentage);
	}

	/**
	 * resizeToDiscSize() <br/>
	 * This method will scale the image to given disc size or max size. <br/>
	 * @param int|null $discSize null will lead to max size
	 */
	public function resizeToDiscSize($discSize = null)
	{
		$this->intern_resizeToDiscSize($discSize);
	}

	/**
	 * resizeToMaxDiskSize() <br/>
	 * This method will scale the image to max upload size. <br/>
	 *
	 */
	public function resizeToMaxDiskSize()
	{
		$this->intern_resizeToDiscSize();
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

	/* =========================================================
	 * Basic / common methods  **internal**
	 * ========================================================= */

	// load
	/**
	 * Load image by MediaItem
	 *
	 * @param MediaItem $item
	 *
	 * @throws \Exception
	 */
	private function loadFromMediaItem(MediaItem $item)
	{
		// set srcPath dependent if absolute path was given or not
		$this->srcPath = $item->fullPath;
		// is the file existing
		if (!file_exists($this->srcPath))
			throw new \Exception("This File does not exist or path ROOT'$item->path' is wrong");
		// load
		$this->intern_load();
	}

	/**
	 * Load image data
	 */
	private function intern_load()
	{
		// gather meta data
		$this->srcInfo = pathinfo($this->srcPath);
		$this->srcSize = filesize($this->srcPath);

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
			default:
				throw new \Exception("Extension not known");
		}

		list($this->srcWidth, $this->srcHeight) = getimagesize($this->srcPath);

		$this->srcOrientation = ($this->srcWidth > $this->srcHeight) ? EImageProcessor::LANDSCAPE : EImageProcessor::PORTRAIT;
		$this->srcAspectRatio = $this->srcWidth / $this->srcHeight;

		// manage transparency
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

	// save
	/**
	 * Save image to $targetPath or overwrite source image
	 *
	 * @param string $targetPath string/to/save or null to overwrite source image
	 * @param bool   $keep flag if the new file should be kept in memory
	 */
	private function intern_save ($targetPath = null)
	{
		if ($this->newImage == null) $this->newImage = $this->srcImage;

		if ($targetPath == null) $targetPath = $this->srcPath;
		else
		{
			if (Pathfinder::isAbsolute($targetPath)) $targetPath = $targetPath;
			$targetPath = $this->dataRootPath . $this->getRelativePath($targetPath);
		}

		// create folders if necessary
		$this->setUpFolder($targetPath);
		// remove file from folder with same name
		$this->intern_removePrevious($targetPath);

		// save
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

	private function intern_removePrevious($newImagePath)
	{
		$pI = pathinfo($newImagePath);
		foreach ($this->possibleExtensions as $possibleExtension) {
				@unlink($pI['dirname'] . '/' . $pI['filename'] . '.' . $possibleExtension);
		}
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

	// helper

	private function getRelativePath($path)
	{
		$path = Pathfinder::cleanPath($path);
		$relPath = '/';
		if (strpos($path, $this->dataRootPath) !== false)
			$relPath = str_replace($this->dataRootPath, '', $path);
		else $relPath = $path;

		return ($relPath[0] == '/') ? $relPath : '/' . $relPath;
	}

	private function getMaxUploadSize()
	{
		if ($this->maxFileSize !== null) return $this->maxFileSize;
		$rawSize = trim(ini_get('upload_max_filesize'));
		$split = preg_split('#(?<=\d)(?=[a-z])#i', $rawSize);
		$last = (isset($split[1])) ? strtolower($split[1]) : '';
		$size = (int) $split[0];
		switch($last) {
			case 'g':
				return $size * pow(1024, 3);
			case 'm':
				return $size * pow(1024, 2);
			case 'k':
				return $size * 1024;
		}
		return $size;
		return $this->maxFileSize = $size;
	}

	private function setUpFolder($targetPath)
	{
		$targetPath = Pathfinder::cleanPath($targetPath);
		// create data root path if not existing
		if (!is_dir($this->dataRootPath)) @mkdir($this->dataRootPath,0755);

		// prepare path information
		$relativePath = $this->getRelativePath($targetPath);
		$folderPath = substr($relativePath, 1);

		$pathParts = explode('/', $folderPath);
		$c = count($pathParts);
		// $fileName = $pathParts[$c-1];
		unset($pathParts[$c-1]);

		// create folder structure
		$i = 0;
		$partPath = '';
		while (isset($pathParts[$i])){
			$partPath .= '/' . $pathParts[$i];
			if (!is_dir($this->dataRootPath . $partPath))
				@mkdir($this->dataRootPath . $partPath,0755);
			$i++;
		}
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
			$this->intern_resize($newWidth, $newHeight, $keepRatio);
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
			$this->intern_resize($newWidth, $newHeight, $keepRatio);
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

	// ======== resize to output disc size ===========
	/**
	 * @param int   $percentage value < 0 minifies value > 0 expands image
	 * @param bool  $save triggers intern_save() if true
	 */
	public function intern_resizeDiscSize($percentage = -10)
	{
		$factor = (100 + $percentage) /100;
		if ($this->srcOrientation == EImageProcessor::LANDSCAPE) $this->resize($this->srcWidth * $factor);
		else $this->resize_height($this->srcHeight * $factor);
	}
	/**
	 * @param int   $discSize
	 */
	public function intern_resizeToDiscSize(int $discSize = null)
	{
		// get upload limit
		$this->getMaxUploadSize();


		$limit = ($discSize == null || $discSize > $this->maxFileSize) ? $this->maxFileSize : $discSize;

		// enlarge until it is to big
		while ($this->srcSize < $limit) {
			$this->intern_resizeDiscSize(10);
			$this->intern_save($this->srcPath);
			$this->load($this->item);
		}

		while ($this->srcSize > $limit) {

			$this->intern_resizeDiscSize(-10);
			$this->intern_save($this->srcPath);
			$this->load($this->item);
		}
	}
}