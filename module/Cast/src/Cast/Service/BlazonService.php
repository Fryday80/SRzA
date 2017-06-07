<?php
namespace Cast\Service;


use Cast\Model\BlazonTable;
use Error;


class BlazonService
{
    const BLAZON_IMAGE_PATH = './data/wappen/';
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

    function __construct(BlazonTable $blazonTable) {
        $this->blazonTable = $blazonTable;
    }

    public function getAll() {
        $this->loadData();
        return $this->data;
    }

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
     * @param $filePath string
     * @param null $bigFilePath
     * @return bool
     */
    public function addNew($name, $filePath, $bigFilePath = null) {
        if ($this->exists($name)) return false;
        $bigFileName = null;
        //move file to wappen folder
        $fileName = $this->moveFile($filePath, $name);
        //@todo! resize file

        if ($bigFilePath) {
            $bigFileName = $this->moveFile($bigFilePath, $name.'_big');
            //@todo! resize file
        }

        $newID = $this->blazonTable->add(array(
            'name' => $name,
            'filename' => $fileName,
            'bigFilename' => $bigFileName
        ));
        //@todo add also to this->data
        return true;
    }

    public function save($id, $name = null, $filePath = null, $bigFilePath = null) {
        $item = $this->getById($id);
        if(!$item) return false;
        $fileName = null;
        $bigFileName = null;
        if ($name !== null && $item['name'] != $name) {
            $fileName = $this->renameItem($item, $name);
            $item['name'] = $name;
        }
        if ($filePath) {
            //@todo! unset old file
            $fileName = $this->moveFile($filePath, $item['name']);
            //@todo! resize file
        }
        if ($bigFilePath) {
            //@todo! unset old file
            $bigFileName = $this->moveFile($bigFilePath, $item['name'].'_big');
            //@todo! resize file
        }
        if ($fileName !== null) $item['filename'] = $fileName;;
        if ($bigFileName !== null) $item['bigFilename'] = $bigFileName;
        $data = [];
        if ($name !== null) $data['name'] = $name;
        if ($fileName !== null) $data['filename'] = $fileName;
        if ($bigFileName !== null) $data['bigFilename'] = $bigFileName;

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
     * @return string file name with extension
     */
    private function moveFile($path, $name) {
        $this->loadData();
        $wappenPath = realpath($this::BLAZON_IMAGE_PATH);
        if (!$wappenPath) {
            //@todo create "wappen" folder in ./data
        }
        $newPath = $wappenPath.'/'.$name.'.'.pathinfo($path, PATHINFO_EXTENSION);
        rename($path, $newPath);
        return pathinfo($newPath, PATHINFO_BASENAME);
    }

    /**
     * @param $item array
     * @param $newName string
     * @return string file name with extension
     */
    private function renameItem(&$item, $newName) {
        $wappenPath = realpath($this::BLAZON_IMAGE_PATH);
        $path = $wappenPath.'/'.$item['filename'];
        $newPath = $wappenPath.'/'.$newName.'.'.pathinfo($path, PATHINFO_EXTENSION);
        rename($path, $newPath);
        $item['name'] = $newName;
        $item['filename'] = pathinfo($newPath, PATHINFO_BASENAME);
        return $item['filename'];
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

}