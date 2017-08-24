<?php
namespace Application\Controller\Plugin;

use Media\Service\ImageProcessor;
use Media\Service\MediaException;
use Media\Service\MediaService;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class ImageUpload extends AbstractPlugin
{
	const DATA_FOLDER_FROM_ROOT = '/Data';

	protected $config;
	/** @var MediaService  */
	protected $mediaService;
	/** @var ImageProcessor  */
	protected $imageProcessor;

	/** @var string path to /Data folder */
	protected $storagePath;
	/** @var  int maximum upload file size */
	protected $maxFileSize;

	function __construct(Array $config, MediaService $mediaService, ImageProcessor $imageProcessor)
	{
		$this->config = $config;
		$this->mediaService = $mediaService;
		$this->imageProcessor = $imageProcessor;
		$this->storagePath = getcwd() . self::DATA_FOLDER_FROM_ROOT;
		$this->storagePath = str_replace('\\', '/', $this->storagePath);
	}

	/**
	 * @param array $uploadData
	 * @param string $destination '/path/to/save.image' <br/>
	 *                            !!leading '/' <br/>
	 *                            relative to data folder
	 *
	 * @throws MediaException
	 */
	public function upload($uploadData, $destination)
	{
		$this->uploadAction($uploadData, $destination);
	}

	/**
	 * @param array $arrayOfUploadData
	 * @param array $arrayOfDestinations with same keys as $arrayOfUploadData
	 */
	public function multiUpload(Array $arrayOfUploadData, Array $arrayOfDestinations)
	{
		$this->multiUploadAction($arrayOfUploadData, $arrayOfDestinations);
	}

	/**
	 * @param array $uploadDataArray from <strong>Form</strong> upload
	 *
	 * @return array|false array ( 0 => file name, 1 => extension )
	 *
	 */
	public function getFileDataFromUpload($uploadDataArray)
	{
		// is it the right array (from Form upload)
		if (!isset($uploadDataArray['name']))
			return false; // @todo throw exception

		$parts = explode ('.', $uploadDataArray['name']);
		$c = count($parts);

		if ($c == 1) return false; // @todo throw exception

		if ($c == 2) return $parts;

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
	 * @param array $uploadData
	 * @param string $destination '/path/to/save.image' <br/>
	 *                            !!leading '/' <br/>
	 *                            relative to data folder
	 *
	 * @throws MediaException
	 */
	protected function uploadAction($uploadData, $destination)
 	{
 		bdump ("works in " . __FUNCTION__ . ' @ Class ' . __CLASS__);
 		die;
		$err = $this->mediaService->upload($uploadData, $this->storagePath . $destination, true);
		if ($err instanceof MediaException) {
			throw $err;
		}
		bdump('fixed');
		die;
  	}

	/**
	 * @param array $arrayOfUploadData
	 * @param array $arrayOfDestinations with same keys as $arrayOfUploadData
	 */
	protected function multiUploadAction(Array $arrayOfUploadData, Array $arrayOfDestinations)
	{
		foreach ($arrayOfUploadData as $key => $uploadData) {
			$this->uploadAction($uploadData, $arrayOfDestinations[$key]);
		}
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
}