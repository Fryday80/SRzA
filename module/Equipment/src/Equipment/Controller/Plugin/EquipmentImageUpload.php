<?php
namespace Application\Controller\Plugin;

use Media\Service\ImageProcessor;
use Media\Service\MediaException;
use Media\Service\MediaService;

class EquipmentImageUpload extends ImageUpload
{

	public function test($string = 'test')
	{
		bdump('function ' . __FUNCTION__ . '() in  ' . __CLASS__);
		bdump($string);
		die;
	}

	/**
	 * @param array $data form data array
	 * @param int   $id
	 *
	 * @return array Equipment item array
	 */
	public function upload($data, $id)
	{
		$id = (int)$id;
		$uploadedImages = $dataTarget = array();

		// check if sth is set
		if ($data['image1'] !== null || $data['image2'] !== null || $data['bill'] !== null){
			// check if set data is string (old upload) or uploadArray => then push to uploadedImages array
			if ($data['image1'] !== null && $this->isUploadArray($data['image1']))
				$uploadedImages['image1'] = $data['image1'];
			if ($data['image2'] !== null && $this->isUploadArray($data['image2']))
				$uploadedImages['image2'] = $data['image2'];
			if ($data['bill']   !== null && $this->isUploadArray($data['bill']  ))
				$uploadedImages['bill']   = $data['bill'];

			// if sth was uploaded
			if ( !empty($uploadedImages) )
			{
				// === create path
				foreach ($uploadedImages as $key => $uploadedImage)
				{
					list ($fileName, $extension) = $this->getFileDataFromUpload($data[$key]);
					$dataTarget[$key] = '/_equipment/' . $id .'/'. $key .'.' . $extension;
					// === upload images
					$this->uploadAction($data[$key], $dataTarget[$key]);
				}
			};


			// write paths to item & return
			return $dataTarget + $data;
		}
		return null; // @todo throw error
	}

	protected function uploadAction($uploadData, $destination)
	{
		$this->imageProcessor->load($uploadData);
		bdump($this->imageProcessor->getLog());die;
		$this->imageProcessor->resizeToMaxDiskSize();


		bdump ( __FUNCTION__ . ' @ Class ' . __CLASS__ . 'EquipmentImageUpload done refactoring now follows parent method'); die;
		parent::uploadAction($uploadData, $destination);
	}
}