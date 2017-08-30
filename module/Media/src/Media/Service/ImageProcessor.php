<?php

namespace Media\Service;

use Media\Model\Enums\EImageProcessor;

/**
 * Class ImageProcessor
 * @package Media\Service
 */
class ImageProcessor
{
	const UPLOAD = 0;
	const OBJECT = 1;
	const PATH   = 2;
	const ITEM_TYPE = array(
		0 => 'upload',
		1 => 'object',
		2 => 'path',
	);

	// defaults
	// defaults set in constructor
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

	//test mode
	private $testMode = false;
	private $i = 0;
	private $testPath;

	// given item
	private $item;

	// source data
	private $srcMeta; // set in constructor

	private $srcSource;
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
		$this->root = $this->cleanPath( $this->config['ImageProcessor']['root'] );
		$this->dataRootPath = $this->cleanPath( $this->config['ImageProcessor']['dataRoot'] );
		$this->srcMeta = array (
			&$this->srcSource,
			&$this->srcImage,
			&$this->srcPath,
			&$this->srcInfo,
			&$this->srcWidth,
			&$this->srcHeight,
			&$this->srcSize,
			&$this->srcOrientation,
			&$this->srcAspectRatio,
		);
	}

	/* =========================================================
	 * Short cuts
	 * ========================================================= */

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
		$this->load($item);

		if ($this->srcWidth == $width && $this->srcHeight == $height){
			$this->intern_save($this->srcPath);
		} else {
			$this->intern_resize($width, $height);
			$this->intern_save();
		}
	}
//
//
//	/**
//	 * Create user image smaller than limits and 2 thumbnail images <br>
//	 * overwrites srcImage and saves thumbs to target paths
//	 *
//	 * @param $srcPath
//	 * @param $targetPathSmall
//	 * @param $targetPathMedium
//	 */
//	public function createUserImages($srcPath, $targetPathSmall, $targetPathMedium)
//	{
//		// get limits for profile image
//		$width  = $this->config['Media_ImageProcessor']['profile_images']['x'];
//		$height = $this->config['Media_ImageProcessor']['profile_images']['y'];
//
//		$this->load($srcPath);
//
//		// if src image is smaller than limits
//		if ($this->srcWidth < $width && $this->srcHeight < $height) {
//			$this->intern_save();
//		}
//		else
//			//resize to limits
//		{
//			if ($this->srcOrientation == EImageProcessor::LANDSCAPE)
//			{
//				$this->intern_resize_width($width);
//				$this->intern_save();
//			}
//			else
//			{
//				$this->intern_resize_height($height);
//				$this->intern_save();
//			}
//		}
//		//create the thumbs
//		$this->createThumbs($srcPath, $targetPathSmall, $targetPathMedium);
//	}
//
//	/**
//	 * Create thumb image
//	 *
//	 * @param string $item 			path/to/image
//	 * @param string $targetPath1	small thumb path, null to overwrite source
//	 * @param string $targetPath2	big thumb path, null to skip
//	 */
//	public function createThumbs($item, $targetPath1, $targetPath2 = null)
//	{
//		$this->load($item);
//
//		// === thumb 1
//		// get size for thumb 1
//		$width = $this->config['Media_ImageProcessor']['thumbs']['x1'];
//		$height = $this->config['Media_ImageProcessor']['thumbs']['y1'];
//
//		$this->intern_resize_crop($width, $height);
//		$this->intern_save($targetPath1);
//		// === thumb 2
//		if ($targetPath2 !== null) {
//			//reload src file
//			$this->intern_load();
//
//			// get size for thumb 2
//			$width = $this->config['Media_ImageProcessor']['thumbs']['x2'];
//			$height = $this->config['Media_ImageProcessor']['thumbs']['y2'];
//
//			$this->intern_resize_crop($width, $height);
//			$this->intern_save($targetPath2);
//		}
//	}
//
//
//	/**
//	 * @param Equip $uploadFormData
//	 * @param int   $itemId
//	 *
//	 * @return array
//	 */
//	public function uploadEquipImages($uploadFormData, $itemId = null)
//	{
//		$mainFolder = '_equipment';
//		$subFolder1 = ($itemId == null) ? $uploadFormData->id : $itemId;
//		$targetFolder = $this->createFolderStructure($this->dataRootPath, $mainFolder, $subFolder1) . '/';
//
//
//		$targetPaths = array();
//		$uploadFormData->id = ($itemId !== null) ? $itemId : $uploadFormData->id;
//		$width  = $this->config['Equipment_ImageProcessor']['images']['x'];
//		$height = $this->config['Equipment_ImageProcessor']['images']['y'];
//
//		$uploadedImages = array();
//		if ($uploadFormData['image1'] !== null) $uploadedImages['image1'] = $uploadFormData['image1'];
//		if ($uploadFormData['image2'] !== null) $uploadedImages['image2'] = $uploadFormData['image2'];
//
//		if (!empty ($uploadedImages))
//		{
//			$targetPath = $targetFolder;
//			$readOutPath = $this->getReadOutPathImage($targetPath);
//
//			foreach ($uploadedImages as $fieldName => $uploadedImage) {
//				$this->load($uploadedImage);
//				$targetFilename = $fieldName . '.' . $this->srcInfo['extension'];
//				$target = $targetPath . $targetFilename ;
//				$readOutTarget = $readOutPath . $targetFilename;
//				// if src image is smaller than limits
//				if (!($this->srcWidth < $width && $this->srcHeight < $height)) {
//					if ($this->srcOrientation == EImageProcessor::LANDSCAPE)
//						$this->resize($width);
//					else
//						$this->resize_height($height);
//				}
//				$this->intern_save($target);
//				$targetPaths[$fieldName] = $readOutTarget;
//			}
//		}
//		return $targetPaths;
//	}

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
	 * Load item to process
	 *
	 * @param object|array|string $item string = relative path, array from form upload, object must be known
	 *
	 */
	public function load($item)
	{
		// save original given data for multiple processing or post processing
		$this->item = $item;
		// load item
		if (is_object($item)) 	$this->loadFromObject($item);
		if (is_array($item))	$this->loadFromUpload($item);
		if (is_string($item))	$this->loadFromPath($this->cleanPath($item));
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
		$this->intern_resize_height($newHeight, $newWidth, $keepRatio);
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

	// load
	/**
	 * Load image by path
	 *
	 * @param string $imagePath
	 *
	 * @throws \Exception
	 */
	private function loadFromPath($imagePath)
	{
		// set srcPath dependent if absolute path was given or not
		$this->srcPath = ($this->isAbsolutePath($imagePath)) ? $imagePath : $this->dataRootPath . $imagePath;
		// is the file existing
		if (!file_exists($this->srcPath))
			throw new \Exception("This File does not exist or path '$imagePath' is wrong");

		// load
		$this->intern_load();
		$this->srcSource = self::PATH;
	}
	/**
	 * Load image data from form upload array
	 *
	 * @param $uploadArray
	 */
	private function loadFromUpload($uploadArray)
	{
		$this->srcPath = $uploadArray['tmp_name'];
		$this->intern_load();
		$this->srcSource = self::UPLOAD;
	}

	/**
	 * Load image data from an object
	 *
	 * @param $imageObject
	 *
	 * @throws \Exception
	 */
	private function loadFromObject($imageObject)
	{
		if ($imageObject instanceof MediaItem)  $this->loadFromMediaItem($imageObject);
		else throw new \Exception("This object is not known");
	}

	/**
	 * Load image by MediaItem
	 *
	 * @param MediaItem $item
	 */
	private function loadFromMediaItem(MediaItem $item)
	{
		$this->loadFromPath($item->fullPath);
		$this->srcSource = self::OBJECT;
	}

	/**
	 * Load image data
	 */
	private function intern_load()
	{
		// gather meta data
		$this->srcInfo = pathinfo($this->srcPath);
		if (!in_array($this->srcInfo['extension'], $this->possibleExtensions))
			$this->getRealExtension();

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

	private function getRealExtension()
	{
		if ($this->srcSource == self::UPLOAD) {
			// get real file extension if upload array was given
			$ext = explode('.', $this->item['name']);
			$c = count($ext);
			$this->srcInfo['extension'] = $ext[ $c - 1 ];
		}
		else throw new \Exception("No upload array");
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

		$targetPath = ($targetPath == null)
			? $this->srcPath
			: ($this->isAbsolutePath($targetPath))? $targetPath : $this->dataRootPath . $this->getRelativePath($targetPath);

		if ($this->testMode) $targetPath = getcwd() . '/public/test.png';

		$this->setUpFolder($targetPath);

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
		$this->intern_removePrevious($targetPath);
		$this->intern_end();
	}

	private function intern_removePrevious($newImagePath)
	{
		$pI = pathinfo($newImagePath);
		foreach ($this->possibleExtensions as $possibleExtension) {
			if ($possibleExtension !== $pI['extension'] && file_exists($pI['dirname'] . '/' . $pI['filename'] . '.' . $possibleExtension))
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
	private function cleanPath($path)
	{
		return str_replace('\\', '/', $path);
	}

	private function isAbsolutePath($path)
	{
		return (!strpos($path, $this->root)) ? : false;
	}

	private function getRelativePath($path)
	{
		$path = $this->cleanPath($path);
		$relPath = '';
		if (strpos($path, $this->dataRootPath) !== false)
			$relPath = str_replace($this->dataRootPath, '', $path);
		else $relPath = $path;

		return ($relPath[0] == '/') ? $relPath : '/' . $relPath;
	}

	private function getMaxUploadSize()
	{
		if ($this->maxFileSize !== null) return $this->maxFileSize;
		$size = trim(ini_get('upload_max_filesize'));

		if ($size === null) return null;
		$last = strtolower($size{strlen($size)-1});
		$size = (int) $size;
		switch($last) {
			case 'g':
				$size *= 1024;
				break;
			case 'm':
				$size *= 1024;
				break;
			case 'k':
				$size *= 1024;
				break;
		}
		return $this->maxFileSize = $size;
	}

	private function setUpFolder($targetPath)
	{
		$targetPath = $this->cleanPath($targetPath);
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