<?php
namespace Cast\Service;


use Cast\Model\BlazonTable;
use Error;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


class BlazonService implements ServiceLocatorAwareInterface
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
    private $serviceLocator;
    private $loaded = false;
    private $data;

    function __construct() {
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

    /**
     * @param $name string
     * @param $isOverlay boolean
     * @param $filePath string
     * @return bool
     */
    public function addNew($name, $isOverlay, $filePath) {
        if ($this->exists($name)) return false;

        //move file to wappen folder
        $fileName = $this->moveFile($filePath, $name);
        //@todo! resize file


        /** @var BlazonTable $blaTable */
        $blaTable = $this->getServiceLocator()->get('Cast\Model\BlazonTable');
        $newID = $blaTable->add(array(
            'name' => $name,
            'isOverlay' => $isOverlay,
            'filename' => $fileName
        ));
        //@todo add also to this->data
        return true;
    }

    public function save($id, $name = null, $isOverlay = null, $filePath = null, $bigFilePath = null, $offsetX = null, $offsetY = null) {
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
        if ($isOverlay !== null) $item['isOverlay'] = $isOverlay;
        if ($fileName !== null) $item['filename'] = $fileName;;
        if ($bigFileName !== null) $item['bigFilename'] = $bigFileName;
        if ($offsetX !== null) $item['offsetX'] = $offsetX;
        if ($offsetY !== null) $item['offsetY'] = $offsetY;
        $data = [];
        if ($name !== null) $data['name'] = $name;
        if ($isOverlay !== null) $data['isOverlay'] = $isOverlay;
        if ($fileName !== null) $data['filename'] = $fileName;
        if ($bigFileName !== null) $data['bigFilename'] = $bigFileName;
        if ($offsetX !== null) $data['offsetX'] = $offsetX;
        if ($offsetY !== null) $data['offsetY'] = $offsetY;

        /** @var BlazonTable $blaTable */
        $blaTable = $this->getServiceLocator()->get('Cast\Model\BlazonTable');
        $blaTable->save($id, $data);
        return $item;
    }

    public function remove($id) {
        $item = $this->getById($id);
        if(!$item) return false;

        /** @var BlazonTable $blaTable */
        $blaTable = $this->getServiceLocator()->get('Cast\Model\BlazonTable');
        if ($blaTable->remove($id) ) {
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

    /**
     * Set service locator
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Get service locator
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator() {
        return $this->serviceLocator;
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
            /** @var BlazonTable $blaTable */
            $blaTable = $this->getServiceLocator()->get('Cast\Model\BlazonTable');
            $this->data = $blaTable->getAll();
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