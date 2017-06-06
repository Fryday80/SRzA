<?php
namespace Album\Service;


use Album\Model\AlbumModel;
use Application\Service\CacheService;
use Media\Service\MediaException;
use Media\Service\MediaService;
use Tracy\Debugger;
use Zarganwar\PerformancePanel\Register;


/**
 * Class GalleryService
 *
 * Manages the db <-> folder mapping
 * for additional (db) information
 */
Class GalleryService
{
    /**
     * @var MediaService
     */
    private $mediaService;
    /** @var  CacheService */
    private $cacheService;
    private $galleryPath = "/gallery";


    function __construct($sm)
    {
        $this->mediaService = $sm->get('MediaService');
        $this->cacheService = $sm->get('CacheService');
    }

    public function getAllAlbums() {
        if ($this->cacheService->hasCache('album')) {
            return $this->cacheService->getCache('album');
        }
        $result = array();
        $galleryDirs = $this->mediaService->getItems($this->galleryPath);
        if ($galleryDirs instanceof MediaException) {
            return $galleryDirs;
        }
        foreach ($galleryDirs as $key => $value) {
            if ($value->readable == 0) continue;
            $album = $this->loadAlbum($value->path);
            if ($album == null) continue;
            array_push($result,  $album);
        }
        $this->cacheService->setCache('album', $result);
        return $result;
    }

    private function loadAlbum($path) {
        $meta = $this->mediaService->getFolderMeta($path);
        if ($meta instanceof MediaException) {
            //@todo trigger system warning $meta->msg;
            return null;
        }
        if (is_array($meta) && isset($meta['Album'])) {
            $a = new AlbumModel($path, $meta);

            if (!($a instanceof AlbumModel)) return null;
            $items = $this->mediaService->getItems($path);
            $result = [];
            foreach ($items as $key => $value) {
                if ($value->readable == 0) continue;
                if ($value->type != 'folder' && $value->type != 'conf') {
                    array_push($result, $value);
                }
            }
            $a->images = $result;


//            $a->loadImages();
            return $a;
        }
    }
    public function getAlbum($name) {
        $path = $this->galleryPath.'/'.$name;
        $meta = $this->mediaService->getFolderMeta($path);
        if (is_array($meta) && isset($meta['Album'])) {
            $album = new AlbumModel($path, $this->mediaService);
            $album->loadImages();
            return $album;
        } else {
            if ($meta instanceof MediaException) {
                dump($meta->msg);
            } else {
                dump("path not exists  -  " . $path);
            }
            return null;
        }
    }

    public function getRandomImage($count = 1) {
        Debugger::timer();
        //get random album
        $galleryDirs = $this->getAllAlbums();
        if (count($galleryDirs) == 0) return [];
        $randomIndex = rand(0, count($galleryDirs) -1);
        $album = $galleryDirs[$randomIndex];
        if (!$album) return [];
        if (count($album) <= $count) {
            return $album->getAllImages();
        }
        $allImages = $album->getAllImages();
        shuffle($allImages);
        $result = [];
        for($i = 0; $i < $count; $i++) {
            array_push($result, $allImages[$i]);
        }
        bdump(Debugger::timer() * 1000);
        return $result;
    }



//    /* read ******************************** */
//    public function getAlbumByID($id) {
//        return $this->albumsTable->getById($id);
//    }
//
//    public function fetchWholeAlbumData($id) {
//        $albums = $this->albumsTable->getById($id);
//        $images = $this->imagesTable->getImagesByAlbumID($id);
//        return array($albums, $images);
//    }
//    public function getImageByID($id) {
//        return $this->imagesTable->getById($id);
//    }
//
///* add ********************************* */
//    /**
//     * @param $data
//     * @return mixed $id on success or bool false on fail
//     */
//    public function addAlbum($data) {
//        return $this->albumsTable->add($data);
//    }
//
//    /**
//     * @param $data
//     * @return mixed $id on single image success, bool false on fail, bool true on image array add
//     */
//    public function addImage($data) {
//        if ($data['id']) {
//            return ($this->imagesTable->add($data));
//        }
//        if ($data[0]['id']){
//            foreach ($data as $image){
//                $this->imagesTable->add($image);
//            }
//            return true;
//        }
//        else {return false;}
//    }
//
//
///* update ********************************* */
//    /**
//     * @param $data array of album information
//     * @return mixed $id on success or bool false on fail
//     */
//    public function updateAlbum($data) {
//        return $this->albumsTable->change($data['id'], $data);
//    }
//
//    /**
//     * Update Image or Images
//     * @param $data array with image data or array of images ([0]=> image data, [1] => image data
//     *
//     * @return mixed $id on success or bool false on fail
//     */
//    public function updateImage($data) {
//        if ($data['id']) {
//            return ($this->imagesTable->change($data['id'], $data));
//        }
//        if ($data[0]['id']){
//            foreach ($data as $image){
//                $this->imagesTable->change($image['id'], $image);
//            }
//            return true;
//        }
//        else {return false;}
//    }
//
///* delete ********************************* */
//    /**
//     * @param $id
//     * @return bool true on success
//     */
//    public function deleteWholeAlbum($id) {
//        $this->deleteAllAlbumImages($id);
//        $this->albumsTable->remove($id);
//        return true;
//    }
//
//    /**
//     * @param $image_id
//     * @return bool true on success or false on fail
//     */
//    public function deleteImage($image_id) {
//        $this->imagesTable->remove($image_id);
//        return $this->albumImagesTable->removeByImageID($image_id);
//    }
//
//    /**
//     * @param $id
//     * @return bool true on success or false on fail
//     */
//    public function deleteAllAlbumImages($id) {
//        $images = $this->imagesTable->getImagesByAlbumID($id);
//        $this->albumImagesTable->removeByAlbumID($id);
//        foreach ($images as $image){
//            $this->imagesTable->remove ($image['id']);
//        }
//        return true;
//    }
//
//    public function storeAlbum ($album_data){
//
//        $album_data = $this->exchangeDataArray($album_data);
//
//        if ($album_data['id'] == '' ){
//            $this->addAlbum($album_data);
//        }
//        else {
//            $this->updateAlbum($album_data);
//        }
//    }
//
//    private function exchangeDataArray($data){
//        $int_array = array (
//            'id', 'timestamp', 'visibility', 'album_id', 'image_id');
//        $exclude_array = array (
//            'validators','submit','date');
//        $new_data = array();
//        foreach ($data as $key => $value){
//            if (in_array($key, $int_array)){
//                $value = (int) $value;
//            }
//
//            if (!in_array($key, $exclude_array)) {
//                $new_data[$key] = $value;
//            }
//        }
//        return $new_data;
//    }
}