<?php
namespace Application\Controller\Plugin;

use const Media\Service\DATA_PATH;
use Media\Service\ImageProcessor;
use Media\Service\MediaException;
use Media\Model\MediaItem;
use Media\Service\MediaService;
use Media\Utility\Pathfinder;
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

	function __construct(Array $config, MediaService &$mediaService)
	{
		$this->config = $config;
		$this->mediaService = &$mediaService;
		$this->imageProcessor = &$mediaService->imageProcessor;

		$this->storagePath = getcwd() . DATA_PATH;
		$this->storagePath = str_replace('\\', '/', $this->storagePath);
	}


	/**
	 * @return MediaException[] | MediaItem[]
	 */
	public function upload($uploadData, $uploadDestinationPath, $uploadFileName = null)
	{
		if (is_array($uploadDestinationPath))
			return $this->multiUpload($uploadData, $uploadDestinationPath, $uploadFileName);
		else
			return array($this->singleUpload($uploadData, $uploadDestinationPath, $uploadFileName));

	}

	public function deleteAllImagesByPath($path)
	{
		$this->mediaService->deleteAllItemsByPath($path);
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
	public function isValidUploadArray(&$imageDataArray){
		if (!$imageDataArray) return false;
		if ( // is upload array
			isset($imageDataArray['name'])     &&
			isset($imageDataArray['type' ])    &&
			isset($imageDataArray['tmp_name']) &&
			isset($imageDataArray['error'])    &&
			isset($imageDataArray['size'])
		){
			// if has no error
			if($imageDataArray['error'] == 0)  // removes failure uploads
				return true;
			// upload with error
			$imageDataArray = null;
		}
	return false;
	}

	protected function multiUpload($uploadData, $uploadDestinationPath, $uploadFileName = null)
	{
		$return = array();
		foreach ($uploadData as $key => $uploadArray) {
			$name = (isset($uploadFileName[$key])) ? $uploadFileName[$key] : null;
			$return[$key] = $this->singleUpload($uploadData[$key], $uploadDestinationPath[$key], $name);
		}
		return $return;
	}

	protected function singleUpload($uploadData, $uploadDestinationPath, $uploadFileName = null) {
		if($uploadDestinationPath[strlen($uploadDestinationPath)-1] !== '/') $uploadDestinationPath .= '/';
		if ($uploadFileName == null){
			list ($fileName, $extension) = $this->getFileDataFromUpload($uploadData);
			$uploadFileName = $fileName . $extension;
		}

		return $this->uploadAction($uploadData, $uploadDestinationPath, $uploadFileName);
	}

	/**
	 * @param array $uploadData
	 * @param       $uploadDestinationPath
	 * @param       $uploadFileName
	 *
	 * @return MediaException|MediaItem
	 * @throws MediaException
	 *
	 * @internal param string $fileName File.name
	 *
	 */
	protected function uploadAction($uploadData, $uploadDestinationPath, $uploadFileName)
 	{
		$uploadHandler = $this->mediaService->uploadHandlerFactory($uploadData, $uploadDestinationPath, true);
		if ($uploadHandler instanceof MediaException) {
			throw $uploadHandler;
		}
		$uploadHandler->autoOverwrite = true;
		$uploadHandler->setName($uploadFileName);
		$item = $this->mediaService->getItem(Pathfinder::getRelativePath($uploadHandler->upload()));
		if ($item instanceof MediaException) {
			throw $item;
		}
		return $item;
  	}

	protected function internalDeleteItem()
	{
		$item = $this->mediaService->getItem($this->uploadDestinationPath.$this->uploadFileName);
		if (!($item instanceof MediaException))
			$this->mediaService->deleteItem($item->path);
	}
}