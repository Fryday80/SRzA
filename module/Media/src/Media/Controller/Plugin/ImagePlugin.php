<?php
namespace Application\Controller\Plugin;

use const Media\Service\DATA_PATH;
use Media\Service\ImageProcessor;
use Media\Service\MediaException;
use Media\Service\MediaItem;
use Media\Service\MediaService;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Class ImageUpload <br/>
 * Controller Plugin to manage Image Uploads
 *
 * @package Application\Controller\Plugin
 */
class ImagePlugin extends AbstractPlugin
{
	protected $config;
	/** @var MediaService  */
	public $mediaService;
	/** @var ImageProcessor  */
	public $imageProcessor;

	/** @var string path to /Data folder */
	protected $storagePath;
	/** @var  int maximum upload file size */
	protected $maxFileSize;

	/** @var bool */
	private $overwrite = true;
	private $uploadData;
	private $uploadDestinationPath;
	private $uploadFileName = null;

	/** @var bool */
	private $uploadArrayCheck = false;
	/** @var bool */
	private $hasUploads = false;
	private $uploadedImages = array();

	function __construct(Array $config, MediaService &$mediaService)
	{
		$this->config = $config;
		$this->mediaService = &$mediaService;
		$this->imageProcessor = &$mediaService->imageProcessor;

		$this->storagePath = getcwd() . DATA_PATH;
		$this->storagePath = str_replace('\\', '/', $this->storagePath);
	}

	// === set data
	/**
	 * Set Data
	 *
	 * @param array $uploadData image upload array | Form data array
	 *
	 * @return $this
	 */
	public function setData($uploadData)
	{
		$this->uploadData = $uploadData;
		return $this;
	}

	/**
	 * Set Destination Path
	 *
	 * @param string $uploadDestinationPath
	 *
	 * @return $this
	 */
	public function setDestination($uploadDestinationPath)
	{
		$this->uploadDestinationPath = $uploadDestinationPath;
		return $this;
	}

	/**
	 * Set Filename <br/>
	 * 		overrides original filename and extension
	 *
	 * @param string $uploadFileName "file.name"
	 *
	 * @return $this
	 */
	public function setFileName($uploadFileName)
	{
		$this->uploadFileName = $uploadFileName;
		return $this;
	}

	public function setOverwriteMode(bool $mode)
	{
		$this->overwrite = $mode;
	}


	// === methods via MediaService
	/**
	 * @return MediaException|\Media\Service\MediaItem
	 */
	public function upload()
	{
		if($this->uploadDestinationPath[strlen($this->uploadDestinationPath)-1] !== '/') $this->uploadDestinationPath .= '/';
		if ($this->uploadFileName == null){
			list ($fileName, $extension) = $this->getFileDataFromUpload($this->uploadData);
			$this->uploadFileName = $fileName . $extension;
		}

		return $this->uploadAction();
	}

	public function delete($item)
	{
		if ($item instanceof MediaItem)
			$this->mediaService->deleteItem($item->path);
		// internal use -> $item is uploadArray
		else
			$this->internalDeleteItem();
	}

	public function deleteAll($path)
	{

	}

	// === prepare
	/**
	 * @param array $uploadDataArray from <strong>Form</strong> upload
	 *
	 * @return array|false array ( 0 => file name, 1 => extension )
	 *
	 */
	public function getFileDataFromUpload(&$uploadDataArray)
	{
		// is it the right array (from Form upload)
		if (!isset($uploadDataArray['name']))
			return false; // @todo throw exception

		$uploadDataArray['name'] = str_replace(' ', '', $uploadDataArray['name']);
		$parts = explode ('.', $uploadDataArray['name']);
		$c = count($parts);

		if ($c == 1) return false; // @todo throw exception

		if ($c == 2) return array(str_replace(' ', '', $parts[0]), $parts[1]);

		// processing if filename if a '.' is in file name
		$extension = $parts[$c-1];
		unset ($parts[$c-1]);
		$name = implode('.', $parts);
		return array($name, $extension);
	}

	/**
	 * @param mixed $imageDataArray <br/>
	 *                              checks for data from <strong>Form</strong>
	 *
	 * @return bool <strong>bool: if </strong>data is array from Form upload <strong>true else false</strong>
	 */
	public function isUploadArray($imageDataArray){
		if (!$imageDataArray) return false;
	if (
		isset($imageDataArray['name'])     &&
		isset($imageDataArray['type' ])    &&
		isset($imageDataArray['tmp_name']) &&
		isset($imageDataArray['error'])    &&
		isset($imageDataArray['size'])
		&&
		$imageDataArray['error'] == 0  // remove failures in upload
	){
		// a file was uploaded
		return true;
	}
	return false;
	}

	/**
	 * Checks recursive if there is a Upload Array in given array <br/>
	 * returns "false" if there was a uploadError detected
	 *
	 * @param $data
	 *
	 * @return bool
	 */
	public function containsUploadArray ($data)
	{
		$this->uploadArrayCheck = true;
		if (!is_array($data)) return false;
		elseif ($this->isUploadArray($data)) {
			$this->uploadedImages[0] = $data;
			$this->hasUploads = true;
		}
		else
			$this->hasUploads = $this->checkForUploadArrayRecursive($data);
		return $this->hasUploads;
	}

	public function getUploadArrays()
	{
		if (!$this->uploadArrayCheck) $this->containsUploadArray($this->uploadData);
		return ($this->hasUploads) ? $this->uploadedImages : array();
	}


	protected function checkForUploadArrayRecursive($array)
	{
		$result = $subResult = false;
		foreach ($array as $key => $value) {
			if (is_array($value))
			{
				if($this->isUploadArray($value))
				{
					$this->uploadedImages[$key] = $value;
					$result = true;
				}
				else
					$subResult = $this->checkForUploadArrayRecursive($value);
				if ($subResult == true) $result = true;
			}
		}
		return $result;

	}

	/**
	 * @param array  $uploadData
	 * @param string $destination '/path/to/save/image' <br/>
	 *                            !!leading '/' <br/>
	 *                            relative to data folder
	 *
	 * @param string $fileName    File.name
	 *
	 * @return MediaException|\Media\Service\MediaItem
	 * @throws MediaException
	 */
	protected function uploadAction()
 	{
 		// overwrite ->delete old item if name is the same
		if ($this->overwrite)
			$this->internalDeleteItem();
		$itemOrError = $this->mediaService->upload($this->uploadData, $this->uploadDestinationPath, $this->uploadFileName, true);
		if ($itemOrError instanceof MediaException) {
			throw $itemOrError;
		}
		return $itemOrError;
  	}

	protected function getMaxUploadSize()
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

	protected function internalDeleteItem()
	{
		$item = $this->mediaService->getItem($this->uploadDestinationPath.$this->uploadFileName);
		if (!($item instanceof MediaException))
			$this->mediaService->deleteItem($item->path);
	}
}