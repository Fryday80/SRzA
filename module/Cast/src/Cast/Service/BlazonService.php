<?php
namespace Cast\Service;

use Cast\Model\BlazonTable;
use Cast\Model\EBlazonDataType;

class BlazonService
{
    const BLAZON_IMAGE_PATH = './Data/wappen/';
    const BLAZON_IMAGE_URL = '/wappen/';

    /** @var  BlazonTable */
    private $blazonTable;

    private $data = false;
    private $dataNoOverlays = false;
    private $dataOverlays = false;

    function __construct(BlazonTable $blazonTable, CastService $castService) {
        $this->blazonTable = $blazonTable;
        $this->castService = $castService;
    }

    /**
     * Get all blazons
     * @return array 'id' => data
     */
    public function getAll() {
        $this->getData(EBlazonDataType::ALL);
        return $this->data;
    }

    /**
     * Get all blazons that are overlays
     * @return array 'id' => data
     */
    public function getAllOverlays() {
        $this->getData(EBlazonDataType::OVERLAY);
        return $this->dataOverlays;
    }

    /**
     * Get all blazons that are no overlays
     * @return array 'id' => data
     */
    public function getAllNoOverlays() {
        $this->getData(EBlazonDataType::NO_OVERLAY);
        return $this->dataNoOverlays;
    }

    /**
     * Get blazon by id
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $this->getData(EBlazonDataType::ALL);
        return (isset($this->data[$id])) ? $this->data[$id] : false;
    }

    /**
     * Get argument array by char
     * @param $char
     * @param bool $familyBlazon
     * @return array array of blazon ids
     */
    public function getArgumentsByChar($char, $familyBlazon = false) {
        $this->getData(EBlazonDataType::ALL);
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
        $this->getData(EBlazonDataType::ALL);
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

    /**
     * @param $name string
     * @param $isOverlay
     * @param null $blazonData
     * @param null $blazonBigData
     * @return bool
     */
    public function addNew($name, $isOverlay, $blazonData = null, $blazonBigData = null) {
        $fileData['fileName'] = $bigFileData['fileName'] = null;
        // small blazon uploaded ?
        if (!($blazonData['error'] > 0)) {
            $fileData = $this->moveFile($blazonData['tmp_name'], $name, $blazonData['name']);
            $this->adjustUploadPic($fileData['filePath']);
        }
        // big blazon uploaded ?
        if (!($blazonBigData['error'] > 0)) {
            $bigFileData = $this->moveFile($blazonBigData['tmp_name'], $name.'_big', $blazonBigData['name']);
            $this->adjustUploadPic($bigFileData['filePath']);
        }

        $this->resetInMemoryCache();
        $newItem = array(
            'isOverlay' => $isOverlay,
            'name' => $name,
            'filename' => $fileData['fileName'],
            'bigFilename' => $bigFileData['fileName']
        );
        return $this->blazonTable->add($newItem);
    }

    /**
     * @param $id
     * @param $isOverlay
     * @param null $name
     * @param null $blazonData
     * @param null $blazonBigData
     * @return bool
     */
    public function save($id, $isOverlay, $name = null, $blazonData = null, $blazonBigData = null) {
        $data['isOverlay'] = (int) $isOverlay;
        $item = $this->getById($id);
        if(!$item) return false;

        $fileData['fileName'] = $bigFileData['fileName'] = null;

        // name changed ?
        if ($name !== null && $item['name'] != $name) {
            if ($item['filename'] !== null)
                $data['fileName'] = $this->renameItem($item, $name);
            if ($item['bigFilename'] !== null)
                $data['bigFilename'] = $this->renameItem($item, $name . '_big');
            $data['name'] = $name;
        }
        // small blazon uploaded ?
        if (!($blazonData['error'] > 0)) {
            $fileData = $this->moveFile($blazonData['tmp_name'], $item['name'], $blazonData['name']);
            $this->adjustUploadPic($fileData['filePath']);
            $data['filename'] = $fileData['fileName'];
        }
        // big blazon uploaded ?
        if (!($blazonBigData['error'] > 0)) {
            $bigFileData = $this->moveFile($blazonBigData['tmp_name'], $item['name'].'_big', $blazonBigData['name']);
            $this->adjustUploadPic($bigFileData['filePath']);
            $data['bigFilename'] = $bigFileData['fileName'];
        }

        $this->blazonTable->save($id, $data);
        $this->resetInMemoryCache();
        return true;
    }

    /**
     * @param $id
     * @return bool
     */
    public function remove($id) {
        $item = $this->getById($id);
        if(!$item) return false;

        if ($this->blazonTable->remove($id) ) {
            //remove file
            $wappenPath = realpath($this::BLAZON_IMAGE_PATH);
            $path = $wappenPath.'/'.$item['filename'];
            @unlink($path);
            $this->resetInMemoryCache();
            return true;
        }
    }

    /** moves file to
     * @param $path string
     * @param $name string filename with extension
     * @param $originalFileName
     * @return string file name with extension
     */
    private function moveFile($path, $name, $originalFileName) {
        $ext = pathinfo($originalFileName, PATHINFO_EXTENSION);
        $blazonPath = realpath($this::BLAZON_IMAGE_PATH);
        if (!$blazonPath) {
            //@todo create "wappen" folder in ./Data
            // not tested
//            mkdir ($blazonPath);
        }
        $newPath = $blazonPath.'/'.$name.'.'.$ext;
        rename($path, $newPath);
        return array(
            'fileName' => pathinfo($newPath, PATHINFO_BASENAME),
            'filePath' => $newPath,
        );
    }

    /**
     * @param $item array
     * @param $newName string
     * @return string file name with extension
     */
    private function renameItem(&$item, $newName) {
        $blazonPath = realpath($this::BLAZON_IMAGE_PATH);
        $path = $blazonPath.'/'.$item['filename'];
        if (file_exists($path)) {
            $newPath = $blazonPath . '/' . $newName . '.' . pathinfo($path, PATHINFO_EXTENSION);
            rename($path, $newPath);
            $item['name'] = $newName;
            $item['filename'] = pathinfo($newPath, PATHINFO_BASENAME);
        return $item['filename'];
        }
        return null;
    }

    private function adjustUploadPic($imagePath) {

        $img = file_get_contents($imagePath);
        $im  = imagecreatefromstring($img);

        ImageAlphaBlending($im, true);

        $width  = imagesx($im);
        $height = imagesy($im);

        if ($width == $height) //save pic
        {
            //@todo
        }
        else // refactor pic
        {
            $newsize = 0;
            if ($width > $height) {
                $newheight   = $newwidth = $width;
                $startWidth  = 0;
                $startHeight = ($newheight - $height) /2;
            }
        else {
            $newheight   = $newwidth = $height;
            $startWidth  = ($newwidth-$width) /2;
            $startHeight = 0;
        }
            $srcInfo = pathinfo($imagePath);
            $blazon = imagecreatetruecolor($newwidth, $newheight);
            $transparent = imagecolortransparent($blazon, imagecolorallocatealpha($blazon, 255, 255, 255, 127));
            imagefill($blazon, 0, 0, $transparent);

            imagecopyresized($blazon, $im, $startWidth, $startHeight, 0, 0, $newwidth, $newheight, $width, $height);
            switch($srcInfo['extension']) {
                case 'jpg':
                case 'jpeg':
                    imagejpeg($blazon, $imagePath);
                    break;
                case 'png':
                    imagepng($blazon, $imagePath);
                    break;
                case 'gif':
                    imagegif($blazon, $imagePath);
            }
            imagedestroy($blazon);
            imagedestroy($im);
        }
    }

    private function getData($type)
    {
        switch ($type){
            case EBlazonDataType::ALL:
                if($this->data) break;
                $data = $this->blazonTable->getAll();
                foreach ($data as $blazonData)
                    $this->data[$blazonData['id']] = $blazonData;
                break;
            case EBlazonDataType::OVERLAY:
                if($this->dataOverlays) break;
                $data = $this->blazonTable->getAllOverlays();
                foreach ($data as $blazonData)
                    $this->dataOverlays[$blazonData['id']] = $blazonData;
                break;
            case EBlazonDataType::NO_OVERLAY:
                if($this->dataNoOverlays) break;
                $data = $this->blazonTable->getAllNotOverlay();
                foreach ($data as $blazonData)
                    $this->dataNoOverlays[$blazonData['id']] = $blazonData;
                break;
        }
    }

    private function resetInMemoryCache()
    {
        $this->data = $this->dataOverlays = $this->dataNoOverlays = false;
    }
}