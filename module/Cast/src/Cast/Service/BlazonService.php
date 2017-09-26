<?php
namespace Cast\Service;

use Application\Controller\Plugin\ImagePlugin;
use Cast\Model\DataModels\Blazon;
use Cast\Model\Tables\BlazonTable;
use Cast\Model\Enums\EBlazonDataType;
use Media\Model\MediaItem;
use Media\Service\MediaException;

class BlazonService
{
    const BLAZON_IMAGE_PATH = './Data/wappen/';
    const BLAZON_IMAGE_URL = '/wappen/';

    /** @var  BlazonTable */
    private $blazonTable;
    /** @var CastService  */
	private $castService;

	// in memory cache
	private $wholeDataLoaded = false;
	private $noOverlaysLoaded = false;
	private $overlaysLoaded = false;
    private $data = null;
    private $dataNoOverlays = null;
    private $dataOverlays = null;
    // image upload
	/** @var  ImagePlugin */
	private $uploader;

	function __construct(BlazonTable $blazonTable, CastService $castService) {
        $this->blazonTable = $blazonTable;
        $this->castService = $castService;
    }

	/* =========================================================
	 * Data read out
	 * ========================================================= */
    /**
     * Get all blazons
     * @return Blazon[] 'id' => data; data is Blazon Object
     */
    public function getAll() {
        $this->loadData(EBlazonDataType::ALL);
        return $this->data;
    }

    /**
     * Get all blazons that are overlays
     * @return Blazon[] 'id' => data; data is Blazon Object
     */
    public function getAllOverlays() {
        $this->loadData(EBlazonDataType::OVERLAY);
        return $this->dataOverlays;
    }

    /**
     * Get all blazons that are no overlays
     * @return Blazon[] 'id' => data; data is Blazon Object
     */
    public function getAllNoOverlays() {
        $this->loadData(EBlazonDataType::NO_OVERLAY);
        return $this->dataNoOverlays;
    }

    /**
     * Get blazon by id
     * @param int $id
     * @return false | Blazon
     */
    public function getById($id) {
    	// if id exists in cache return value
    	$item = (isset($this->data[$id])) ? $this->data[$id] : false;
    	// else load single entry from db to cache
    	if (!$item)
        	$this->loadData(EBlazonDataType::SINGLE, $id);
		// return item or false
        return (isset($this->data[$id])) ? $this->data[$id] : false;
    }

    /* ==========================================================
     * load and in memory caching
     * ==========================================================*/

	private function loadData($type, $id = null)
	{
		if ($this->wholeDataLoaded) return;
		$data = null;

		switch ($type){
			case EBlazonDataType::ALL:
					$data = $this->blazonTable->getAll();
					$this->wholeDataLoaded = $this->noOverlaysLoaded = $this->overlaysLoaded = true;
				break;
			case EBlazonDataType::OVERLAY:
				if (!$this->overlaysLoaded) {
					$data = $this->blazonTable->getAllOverlays();
					$this->overlaysLoaded = true;
				}
				break;
			case EBlazonDataType::NO_OVERLAY:
				if (!$this->noOverlaysLoaded) {
					$data = $this->blazonTable->getAllNotOverlay();
					$this->noOverlaysLoaded = true;
				}
				break;
			case EBlazonDataType::SINGLE:
				$data[0] = $this->blazonTable->getById($id);
				if ($data[0] == false) $data = false;
				break;
		}

		if ($data !== null && $data !== false){
			foreach ($data as $blazonData)
				$this->addItem2Cache($blazonData);
		}
	}

	private function addItem2Cache(Blazon $blazonData)
	{
		$blazonId = (int) $blazonData->id;
		// add to cache of all blazons
		$this->data[ $blazonId ] = $blazonData;
		// add to cache of overlays | no overlays
		if ($blazonData->isOverlay == 0) $this->dataNoOverlays[$blazonId] = $blazonData;
		else $this->dataOverlays[$blazonId] = $blazonData;
	}

	private function updateCache(Blazon $blazonData, $id = null)
	{
		if ($id !== null) $blazonData->id = (int) $id;
		$this->data[$blazonData->id] = $blazonData;
	}

	private function removeFromCache($id)
	{
		$id = (int) $id;
		unset ($this->data[$id]);
		if (isset ($this->dataNoOverlays[$id])) unset ($this->dataNoOverlays[$id]);
		if (isset ($this->dataOverlays[$id])) unset ($this->dataOverlays[$id]);
	}

	private function resetInMemoryCache()
	{
		$this->wholeDataLoaded  = false;
		$this->overlaysLoaded   = false;
		$this->noOverlaysLoaded = false;
		$this->data 			= null;
		$this->dataOverlays 	= null;
		$this->dataNoOverlays 	= null;
	}

    /* =========================================================
     * BlazonHelper methods
     * ========================================================= */
    /**
     * Get argument array by char
     * @param $char
     * @param bool $familyBlazon
     * @return array array of blazon ids
     */
    public function getArgumentsByChar($char, $familyBlazon = false) {
        $this->loadData(EBlazonDataType::ALL);
        if ($familyBlazon)
            $base = ($char['family_blazon_id'] !== 0) ? $char['family_blazon_id'] : 1;
        else
            $base  = ($char['blazon_id'] !== 0) ? $char['blazon_id'] : 1;
        $over1 = ($base == 1 && isset($char['job_blazon_id'])) ? $char['job_blazon_id'] : 0;
        if (isset($char['supervisor_id']))
        {
            $supervisorBlazonId = $this->getSupervisorBlazon($char['supervisor_id'])['blazon_id'];
        }
        $over2 = (isset($this->data[$supervisorBlazonId])) ? $supervisorBlazonId : 0;

        return array($base, $over1, $over2);
    }

    /**
     * Get filenames for BlazonHelper
     * @param array $arg array of blazon ids
     * @param bool $familyBlazon true if family blazon
     * @return array array of file names
     */
    public function getFilenames(array $arg, $familyBlazon = false) {
        $this->loadData(EBlazonDataType::ALL);
        if ($familyBlazon)
            $base  = (isset($this->data[$arg[0]]['bigFilename'])) ? $this->data[$arg[0]]['bigFilename'] : $this->data[$arg[0]]['filename'];
        else
            $base  = (isset($this->data[$arg[0]]['filename'])) ? $this->data[$arg[0]]['filename'] : $this->data[1]['filename'];
        if ($arg[1] == 0) $over1 = '';
        else
            $over1 = (isset($this->data[$arg[1]]['filename'])) ? $this->data[$arg[1]]['filename'] : '';
        if ($arg[2] == 0 || $arg[2] == 1) $over2 = '';
        else
            $over2 = (isset($this->data[$arg[2]]['bigFilename'])) ? $this->data[$arg[2]]['bigFilename'] : $this->data[$arg[2]]['filename'];

        return array($base, $over1, $over2);
    }

    /**
     * Get character data of next supervisor with own blazon
     * @param int $supervisor_id
     * @return array of next supervisor with own blazon
     */
    private function getSupervisorBlazon(int $supervisor_id) {
        $supervisor = $this->castService->getCharacterDataById($supervisor_id);
        if ($supervisor['blazon_id'] == 0 || $supervisor['blazon_id'] == null || !isset($supervisor['blazon_id']))
            if($supervisor['supervisor_id'] !== null)
            $supervisor = $this->getSupervisorBlazon($supervisor['supervisor_id']);
        return $supervisor;
    }

	/* =========================================================
	 * Add, edit, delete
	 * ========================================================= */
	public function setImageUploadPlugin(ImagePlugin $imageUploadPlugin)
	{
		$this->uploader = $imageUploadPlugin;
	}

    /**
	 * Add new blazon
	 *
     * @param string $name
     * @param int    $isOverlay
     * @param array  $blazonData    form upload array
     * @param array  $blazonBigData form upload array
     * @return bool|int new id or false
     */
    public function addNew($name, $isOverlay, $blazonData = null, $blazonBigData = null)
	{
		$isOverlay = (int) $isOverlay;

		list($fileName, $bigFileName) = $this->uploadImages($name, $blazonData, $blazonBigData);

        $newItem = new Blazon();
        $newItem->appendDataArray(
        	array(
				'isOverlay' => $isOverlay,
				'name' => $name,
				'filename' => $fileName,
				'bigFilename' => $bigFileName
			)
		);
        $newId = $this->blazonTable->add($newItem);
        $newItem->id = (int) $newId;
        $this->addItem2Cache($newItem);
        return $newItem->id;
    }

	/**
	 * Save edited blazon
	 *
	 * @param int  $id
	 * @param int  $isOverlay
	 * @param null $name
	 * @param null $blazonData
	 * @param null $blazonBigData
	 *
	 * @return bool
	 * @throws MediaException
	 */
    public function save($id, $isOverlay, $name = null, $blazonData = null, $blazonBigData = null)
	{
		$id = (int) $id;
		/** @var Blazon $item */
        $item = $this->getById($id);

        if($blazonData['error'] > 0) $blazonData = null;
        if($blazonBigData['error'] > 0) $blazonBigData = null;

        if(!$item) return false;

        // get copy of original data
		$newItem = $item;
		$newItem->isOverlay = (int) $isOverlay;
        // name changed ?
        if ($name !== null && $item['name'] !== $name) {
            if ($item->filename !== null && $blazonData == null) {
				$newItem->filename = $this->uploader->rename($item->filename, $name);
				if ($newItem->filename instanceof MediaException)
					throw $newItem->filename;
				elseif ($newItem->filename instanceof MediaItem)
					$newItem->filename = $newItem->filename->path;
			}

            if ($item->bigFilename !== null && $blazonBigData == null) {
				$newItem->bigFilename = $this->uploader->rename($item->bigFilename, $name . '_big');
				if ($newItem->bigFilename instanceof MediaException)
					throw $newItem->bigFilename;
				elseif ($newItem->bigFilename instanceof MediaItem)
					$newItem->bigFilename = $newItem->bigFilename->path;
			}

			$newItem->name = $name;
        }

		list($fileName, $bigFileName) = $this->uploadImages($name, $blazonData, $blazonBigData);
        if ($fileName !== null)    $newItem->filename    = $fileName;
        if ($bigFileName !== null) $newItem->bigFilename = $bigFileName;
		$this->updateCache($newItem);
        $this->blazonTable->save($newItem);
        return true;
    }

    /**
	 * Remove blazon
	 *
     * @param $id
     * @return bool
     */
    public function remove($id) {
        $item = $this->getById($id);
        if(!$item) return false;

        if ($this->blazonTable->remove($id) ) {
            //remove file
			if ($item->filename !== null)
				$this->uploader->deleteAllImagesByPath($item->filename);

			if ($item->bigFilename !== null)
				$this->uploader->deleteAllImagesByPath($item->bigFilename);

            $this->removeFromCache($id);

            return true;
        }
        return false;
    }

	protected function uploadImages($name, $blazonData = null, $blazonBigData = null)
	{
		$uploadedImages = $uploadPath = $uploadName = $blazonItems = array();
		// small blazon uploaded, no errors AND sth given ?
		if ($blazonData !== null && !($blazonData['error'] > 0)) {
			$uploadedImages[0] = $blazonData;
			$uploadPath[0] = self::BLAZON_IMAGE_URL;
			$uploadName[0] = $name;
		}
		// big blazon uploaded, no errors  AND sth given ?
		if ($blazonBigData !== null && !($blazonBigData['error'] > 0)) {
			$uploadedImages[1] = $blazonBigData;
			$uploadPath[1] = self::BLAZON_IMAGE_URL;;
			$uploadName[1] = $name . '_big';
		}

		if(!empty($uploadedImages))
			$blazonItems = $this->uploader->upload($uploadedImages, $uploadPath, $uploadName);

		$fileName    = (isset($blazonItems[0]) && $blazonItems[0] !== null && !($blazonItems[0] instanceof MediaException) ) ? $blazonItems[0]->path : null;
		$bigFileName = (isset($blazonItems[1]) && $blazonItems[1] !== null && !($blazonItems[1] instanceof MediaException) ) ? $blazonItems[1]->path : null;

		if ($fileName !== null) $this->resizeImage($fileName);
		if ($bigFileName !== null) $this->resizeImage($bigFileName);

		return array($fileName, $bigFileName);
    }

	/**
	 * Do the resizing for blazons
	 *
	 * @param $path
	 *
	 * @internal ImageProcessor $iP
	 */
	protected function resizeImage($path)
	{
		$iP = &$this->uploader->imageProcessor;
		$iP->load($this->uploader->mediaService->getItem($path));
		$iP->resize_square(500);
		$iP->saveImage();
    }
}