<?php
namespace Cast\Service;


use Cast\Model\BlazonTable;
use Error;


class BlazonService
{
    const BLAZON_IMAGE_PATH = './Data/wappen/';
    const BLAZON_IMAGE_URL = '/wappen/';
    const ERROR_ID_NOT_FOUND = 0;
    const ERROR_NAME_NOT_FOUND = 1;
    const ERROR_MSG = [
        'ERROR_ID_NOT_FOUND',
        'ERROR_NAME_NOT_FOUND'
    ];
    public $lastError;
    private $loaded = false;
    private $data;
    /** @var  BlazonTable */
    private $blazonTable;
    private $parentBlazons = false;

    function __construct(BlazonTable $blazonTable, CastService $castService) {
        $this->blazonTable = $blazonTable;
        $this->castService = $castService;
    }

    public function getAll() {
        $this->loadData();
        return $this->data;
    }

    /**
     * @param $id
     * @return mixed bool|array
     */
    public function getById($id) {
        $this->loadData();
        foreach ($this->data as $value) {
            if ($id == $value['id']) return $value;
        }
        $lastError = $this::ERROR_ID_NOT_FOUND;
        return false;
    }

    public function getByName($name) {
        $this->loadData();
        foreach ($this->data as $value) {
            if ($name == $value['name']) return $value;
        }
        $lastError = $this::ERROR_NAME_NOT_FOUND;
        return false;
    }

    public function createReference($char){
        $this->parentBlazons[$char['id']] = $char['blazon_id'];
        if (isset($char['employ'])){
            foreach ($char['employ'] as $employee){
                $this->createReference($employee);
            }
        }
        $this->getParentBlazons();
    }

    public function getParentBlazons()
    {
        if(!$this->parentBlazons) $this->createReference($this->castService->getStanding());
        return $this->parentBlazons;
    }

    public function resetParentBlazons()
    {
        $this->parentBlazons = false;
    }

    public function getBlazonHelperArgumentsByCharacter($character){
        if(!$this->parentBlazons)$this->parentBlazons = $this->getParentBlazons();
        $overlay1 = $overlay2 = '';
        $select = (isset($character['job_name'])) ? 'job_name' : 'job_id';
        if ($character[$select] !== null){
            $overlay1 = $character[$select];
        }
        if ( $character['supervisor_id'] !== "0" ) { //not set should be unused when in use
            if ( $character['supervisor_id'] !== "1" ) {    //first level Chars under fictive supervisor
                $overlay2 = (int)$this->parentBlazons[$character['supervisor_id']];
            }
        }
        if (isset($character['blazon_id'])) {
            $base = ($character['blazon_id'] == "0") ? 'standard' : (int)$character['blazon_id'];
        } else {
            $base = 'standard';
        }

        if ($character['id'] == "1") $overlay1 = 'king'; //special rule for the king
        if ($base == $overlay2) $overlay2 = null;

        return array($base, $overlay1, $overlay2);
    }

    public function getBigBlazonUrl($selector){
        if (is_string( $selector )) {//ja da giebt es sicher ne einfache lösung man muss sich nur überlegen mit welche funktion es am geschicktesten geht
            $blazonData = $this->getByName($selector);
        }
        elseif (is_int( $selector )) {
            $blazonData = $this->getById($selector);
        }
        else {
            $blazonData = $this->getByName('standard');
        }
        $fileName = (isset($blazonData['bigFilename'])) ? $blazonData['bigFilename'] : $blazonData['filename'];

        return '/media/file'.$this::BLAZON_IMAGE_URL.$fileName;
    }

    public function getBlazonUrl($selector){
        if (is_string( $selector )) {
            $blazonData = $this->getByName($selector);
        }
        elseif (is_int( $selector )) {
            $blazonData = $this->getById($selector);
        }
        else {
            $blazonData = $this->getByName('standard');
        }
        $fileName = $blazonData['filename'];

        return '/media/file'.$this::BLAZON_IMAGE_URL.$fileName;
    }

    /**
     * @param $name string
     * @param null $blazonData
     * @param null $blazonBigData
     * @return bool
     */
    public function addNew($name, $blazonData = null, $blazonBigData = null) {
        if ($this->exists($name)) return false;
        $fileData['fileName'] = null;
        $bigFileData['fileName'] = null;
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

        $newID = $this->blazonTable->add(array(
            'name' => $name,
            'filename' => $fileData['fileName'],
            'bigFilename' => $bigFileData['fileName']
        ));
        //@todo add also to this->data
        return true;
    }

    public function save($id, $name = null, $blazonData = null, $blazonBigData = null) {
        $item = $this->getById($id);
        if(!$item) return false;
        $fileData['fileName'] = null;
        $bigFileData['fileName'] = null;
        // name changed ?
        if ($name !== null && $item['name'] != $name) {
            $fileData['fileName'] = $this->renameItem($item, $name);
            $bigFileData['fileName'] = $this->renameItem($item, $name . '_big');
            $item['name'] = $name;
        }
        // small blazon uploaded ?
        if (!($blazonData['error'] > 0)) {
            $fileData = $this->moveFile($blazonData['tmp_name'], $item['name'], $blazonData['name']);
            $this->adjustUploadPic($fileData['filePath']);
        }
        // big blazon uploaded ?
        if (!($blazonBigData['error'] > 0)) {
            $bigFileData = $this->moveFile($blazonBigData['tmp_name'], $item['name'].'_big', $blazonBigData['name']);
            $this->adjustUploadPic($bigFileData['filePath']);
        }
        $data = [];
        if ($name !== null) $data['name'] = $name;
        if ($fileData['fileName'] !== null) $data['filename'] = $fileData['fileName'];
        if ($bigFileData['fileName'] !== null) $data['bigFilename'] = $bigFileData['fileName'];

        $this->blazonTable->save($id, $data);
        //@todo add also to this->data
        return $item;
    }

    public function remove($id) {
        $item = $this->getById($id);
        if(!$item) return false;

        if ($this->blazonTable->remove($id) ) {
            //remove file
            $wappenPath = realpath($this::BLAZON_IMAGE_PATH);
            $path = $wappenPath.'/'.$item['filename'];
            @unlink($path);
            return true;
        }
    }

    /**
     * @param $name string
     * @return bool
     */
    public function exists($name) {
        $this->loadData();
        foreach ($this->data as $value) {
            if ($name == $value['name']) return true;
        }
        return false;
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
        $wappenPath = realpath($this::BLAZON_IMAGE_PATH);
        if (!$wappenPath) {
            //@todo create "wappen" folder in ./Data
            // not tested
//            mkdir ($wappenPath);
        }
        $newPath = $wappenPath.'/'.$name.'.'.$ext;
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

    private function loadData() {
        if (!$this->loaded) {
            $this->data = $this->blazonTable->getAll();
            foreach ($this->data as $value) {
                $value['url'] = $this::BLAZON_IMAGE_URL.$value['filename'];
            }
            $this->loaded = true;
        }
    }

    private function error($code) {
        return new Error($this::ERROR_MSG[$code], $code);
    }
    private function adjustUploadPic($imagePath){

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
}