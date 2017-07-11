<?php
namespace Cast\Service;

use Cast\Model\BlazonTable;

class BlazonService
{
    const BLAZON_IMAGE_PATH = './Data/wappen/';
    const BLAZON_IMAGE_URL = '/wappen/';

    /** @var  BlazonTable */
    private $blazonTable;

    private $data;
    private $dataNoOverlays;
    private $dataJustOverlays;

    function __construct(BlazonTable $blazonTable, CastService $castService) {
        $this->blazonTable = $blazonTable;
        $this->castService = $castService;
        $this->loadData();
    }

    public function getAll() {
        return $this->data;
    }

    public function getAllOverlays() {
        return $this->dataJustOverlays;
    }

    public function getAllNoOverlays() {
        return $this->dataNoOverlays;
    }

    /**
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        return (isset($this->data[$id])) ? $this->data[$id] : false;
    }

    public function getArgumentsByChar($char, $familyBlazon = false) {
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

    public function getHTMLArguments($arg, $familyBlazon = false) {
        if ($familyBlazon)
            $base  = (isset($this->data[$arg[0]]['bigFilename'])) ? $this->data[$arg[0]]['bigFilename'] : $this->data[$arg[0]]['filename'];
        else
            $base  = $this->data[$arg[0]]['filename'];
        if ($arg[1] == 0) $over1 = '';
        else
            $over1 = (isset($this->data[$arg[1]]['filename'])) ? $this->data[$arg[1]]['filename'] : '';
        if ($arg[2] == 0 || $arg[2] == 1) $over2 = '';
        else
            $over2 = (isset($this->data[$arg[2]]['bigFilename'])) ? $this->data[$arg[2]]['bigFilename'] : $this->data[$arg[2]]['filename'];

        return array($base, $over1, $over2);
    }

    private function getSupervisorBlazon($supervisor_id) {
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

        $newItem = array(
            'isOverlay' => $isOverlay,
            'name' => $name,
            'filename' => $fileData['fileName'],
            'bigFilename' => $bigFileData['fileName']
        );
        $newItem['id'] = $this->blazonTable->add($newItem);
        $this->data[$newItem['id']] = $newItem;
        return $newItem['id'];
    }

    public function save($id, $isOverlay, $name = null, $blazonData = null, $blazonBigData = null) {
        $data['isOverlay'] = $isOverlay;
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
        $this->loadData();
        return $this->data[$id];
    }

    public function remove($id) {
        $item = $this->getById($id);
        if(!$item) return false;

        if ($this->blazonTable->remove($id) ) {
            //remove file
            $wappenPath = realpath($this::BLAZON_IMAGE_PATH);
            $path = $wappenPath.'/'.$item['filename'];
            @unlink($path);
            $this->loadData();
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
        $this->loadData();
        $ext = pathinfo($originalFileName, PATHINFO_EXTENSION);
        $blazonPath = realpath($this::BLAZON_IMAGE_PATH);
        if (!$blazonPath) {
            //@todo create "wappen" folder in ./Data
            // not tested
//            mkdir ($wappenPath);
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
        $wappenPath = realpath($this::BLAZON_IMAGE_PATH);
        $path = $wappenPath.'/'.$item['filename'];
        if (file_exists($path)) {
            $newPath = $wappenPath . '/' . $newName . '.' . pathinfo($path, PATHINFO_EXTENSION);
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

    private function loadData() {
        $all = $this->blazonTable->getAll();
        foreach ($all as $value) {
            $value['url'] = $this::BLAZON_IMAGE_URL.$value['filename'];
            $this->data[$value['id']] = $value;
            if ($value['isOverlay'] == 1) {
                $this->dataJustOverlays[$value['id']] = $value;
            } else {
                $this->dataNoOverlays[$value['id']] = $value;
            }
        }
    }
}