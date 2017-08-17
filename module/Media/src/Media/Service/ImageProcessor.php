<?php

namespace Media\Service;
use Equipment\Model\DataModels\Equip;
use Media\Model\Enums\EImageProcessor;

/**
 * Class ImageProcessor
 * @package Media\Service
 */
class ImageProcessor
{
	// defaults
	private $readOutPath = "/media/file";
	private $dataRootPath; // default set in constructor

	// debugging
	public $log = array();
	private $loggingMode = true;

	// configuration
	private $config;
	private $possibleExtensions = array (
		'jpg', 'jpeg',
		'gif',
		'png',
	);

	//test mode
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
		$this->dataRootPath = getcwd() . '/Data';
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

		$this->load($srcPath);

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
	public function createThumbs($item, $targetPath1, $targetPath2 = null)
	{
		$this->load($item);

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
		$this->load($item);

		if ($this->srcWidth == $width && $this->srcHeight == $height){
			$this->intern_save($this->srcPath);
		} else {
			$this->intern_resize($width, $height);
			$this->intern_save();
		}
	}

	/**
	 * @param Equip $uploadFormData
	 * @param int   $itemId
	 *
	 * @return array
	 */
	public function uploadEquipImages($uploadFormData, $itemId = null)
	{
		$mainFolder = '_equipment';
		$subFolder1 = ($itemId == null) ? $uploadFormData->id : $itemId;
		$targetFolder = $this->createFolderStructure($this->dataRootPath, $mainFolder, $subFolder1) . '/';


		$targetPaths = array();
		$uploadFormData->id = ($itemId !== null) ? $itemId : $uploadFormData->id;
		$width  = $this->config['Equipment_ImageProcessor']['images']['x'];
		$height = $this->config['Equipment_ImageProcessor']['images']['y'];

		$uploadedImages = array();
		if ($uploadFormData['image1'] !== null) $uploadedImages['image1'] = $uploadFormData['image1'];
		if ($uploadFormData['image2'] !== null) $uploadedImages['image2'] = $uploadFormData['image2'];

		if (!empty ($uploadedImages))
		{
			$targetPath = $targetFolder;
			$readOutPath = $this->getReadOutPathImage($targetPath);

			foreach ($uploadedImages as $fieldName => $uploadedImage) {
				$this->load($uploadedImage);
				$targetFilename = $fieldName . '.' . $this->srcInfo['extension'];
				$target = $targetPath . $targetFilename ;
				$readOutTarget = $readOutPath . $targetFilename;
				// if src image is smaller than limits
				if (!($this->srcWidth < $width && $this->srcHeight < $height)) {
					if ($this->srcOrientation == EImageProcessor::LANDSCAPE)
						$this->resize($width);
					else
						$this->resize_height($height);
				}
				$this->intern_save($target);
				$targetPaths[$fieldName] = $readOutTarget;
			}
		}
		return $targetPaths;
	}

	/* =========================================================
	 * API
	 * ========================================================= */

	public function setDataRootPath($dataRootPath)
	{
		$this->dataRootPath = $dataRootPath;
	}

	public function setReadOutPath($readOutPath)
	{
		$this->readOutPath = $readOutPath;
	}

	public function getDataRootPath()
	{
		return $this->dataRootPath;
	}

	public function getReadOutPath()
	{
		return $this->readOutPath;
	}

	public function getReadOutPathImage($imagePath)
	{
			return str_replace($this->dataRootPath, $this->readOutPath , $imagePath);
	}

	public function createFolderStructure($dataRootPath, $mainFolder = null, $subFolder1 = null, $subFolder2 = null, $subFolder3 = null)
	{
		// full target path is given
		if ($mainFolder == null)
		{
			$path = $this->dataRootPath;
			if (!is_dir($path)) @mkdir($path,0755);
			$sub = str_replace($this->dataRootPath, '', $dataRootPath);
			if (strlen($sub) > 1){
				$sub = str_replace('\\', '/', $sub);
				$subParts = explode('/', $sub);
				foreach ($subParts as $part){
					$path .= '/' . $part;
					if (!is_dir($path)) @mkdir($path,0755);
				}
			}
			return $path;
		}
		// target path is given bitwise
		else
		{
			$path = $dataRootPath;
			if (!is_dir($path)) @mkdir($path,0755);
			$path .= '/' . $mainFolder;
			if (!is_dir($path)) @mkdir($path,0755);
			if ($subFolder1 !== null)
			{
				$path .= '/' . $subFolder1;
				if (!is_dir($path)) @mkdir($path, 0755);
			}
			if ($subFolder2 !== null)
			{
				$path .= '/' . $subFolder2;
				if (!is_dir($path)) @mkdir($path, 0755);
			}
			if ($subFolder3 !== null)
			{
				$path .= '/' . $subFolder3;
				if (!is_dir($path)) @mkdir($path, 0755);
			}

		}
		return $path;
	}


	public function load($item)
	{
		$this->log(__FUNCTION__);
		if (is_object($item)) 	$this->loadFromObject($item);
		if (is_array($item))	$this->loadFromUpload($item);
		if (is_string($item))	$this->loadFromPath($item);
		$this->log(__FUNCTION__);
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

	private function loadFromUpload($uploadArray)
	{
		$this->srcPath = $uploadArray['tmp_name'];
		$this->intern_load($uploadArray['name']);
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
		if ($imageObject instanceof MediaItem) $this->loadFromMediaItem($imageObject);
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
	}

	/**
	 * Load image by path
	 *
	 * @param string $imagePath
	 *
	 * @throws \Exception
	 */
	private function loadFromPath($imagePath)
	{
		if (!file_exists($imagePath)) throw new \Exception("This File does not exist or path '$imagePath' is wrong");
		else {
			$this->srcPath = $imagePath;
			$this->intern_load();
		}
	}

	/**
	 * Load image data
	 *
	 * @param null $fileName
	 */
	private function intern_load($fileName = null)
	{
		$this->log(__FUNCTION__);
		$this->srcInfo = pathinfo($this->srcPath);
		if ($fileName !== null){
			$ext = explode ('.', $fileName);
			$c = count($ext);
			$this->srcInfo['extension'] = $ext[$c-1];
		}
		switch ($this->srcInfo['extension']){
			case 'png':
				$this->srcImage  = imagecreatefrompng($this->srcPath);
				$this->log(__FUNCTION__,'load from png');
				break;
			case 'jpg':
			case 'jpeg':
				$this->srcImage  = imagecreatefromjpeg($this->srcPath);
				$this->log(__FUNCTION__,'load from jpg/jpeg');
				break;
			case 'gif':
				$this->srcImage  = imagecreatefromgif($this->srcPath);
				$this->log(__FUNCTION__,'load from gif');
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
		$this->log(__FUNCTION__);
	}

	/**
	 * Save image to $targetPath or overwrite source image
	 *
	 * @param string $targetPath string/to/save or null to overwrite source image
	 */
	private function intern_save ($targetPath = null)
	{
		$this->log(__FUNCTION__);
		if ($targetPath == null) 	 $targetPath 	 = $this->srcPath;
		if ($this->newImage == null) $this->newImage = $this->srcImage;
		if ($this->testMode) 		 $targetPath 	 = getcwd() . '/public/test.png';
		$this->log(__FUNCTION__, '$targetPath = ' .$targetPath);

		switch($this->srcInfo['extension']) {
			case 'jpg':
			case 'jpeg':
				imagejpeg ( $this->newImage, $targetPath);
			$this->log(__FUNCTION__,'save as jpg/jpeg');
				break;
			case 'png':
				imagepng  ( $this->newImage, $targetPath);
				$this->log(__FUNCTION__,'save as png');
				break;
			case 'gif':
				imagegif  ( $this->newImage, $targetPath);
				$this->log(__FUNCTION__,'save as gif');
		}
		$this->intern_removePrevious($targetPath);
		$this->intern_end();
		$this->log(__FUNCTION__);
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
		$this->log['intern_resize_width'][] = 'triggered';
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
		$this->log['intern_resize_height'][] = 'triggered';
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
		$this->log['intern_resize'][] = 'triggered';
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
		$this->log['intern_resize_crop'][] = 'triggered';
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

	/* ==========================================================
	 * internals
	 * ========================================================== */
	private function log($method, $msg = null, $key = null)
	{
		if ($this->loggingMode) {
			if ($msg == null)
				$msg = (key_exists($method, $this->log)) ? 'done' : 'start';
			if ($key == null)
				$this->log[ $method ][] = $msg;
			else
				$this->log[ $method ][ $key ] = $msg;
		}
	}
}