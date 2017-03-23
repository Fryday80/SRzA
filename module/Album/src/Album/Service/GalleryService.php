<?php
namespace Album\Service;


use Album\Model\AlbumModel;
use Media\Service\MediaService;



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
    private $galleryPath = "/gallery";


    function __construct($sm)
    {
        $this->mediaService = $sm->get('MediaService');
    }



    public function getAllAlbums() {
        $result = array();
        $galleryDirs = $this->mediaService->getFolderNames($this->galleryPath);
        foreach ($galleryDirs as $key => $value) {
            $fileName = '/album.conf';
            if ($this->mediaService->fileExists($value['path'].$fileName) ) {
                $album_conf = parse_ini_file($value['fullPath'].$fileName, TRUE)['Album'];

                array_push($result, new AlbumModel($album_conf['Album']['name'], $album_conf['Album']['description'], []));
            }
        }
        return $result;
    }













    /* read ******************************** */
    public function getAlbumByID($id) {
        return $this->albumsTable->getById($id);
    }

    public function fetchWholeAlbumData($id) {
        $albums = $this->albumsTable->getById($id);
        $images = $this->imagesTable->getImagesByAlbumID($id);
        return array($albums, $images);
    }
    public function getImageByID($id) {
        return $this->imagesTable->getById($id);
    }

/* add ********************************* */
    /**
     * @param $data
     * @return mixed $id on success or bool false on fail
     */
    public function addAlbum($data) {
        return $this->albumsTable->add($data);
    }

    /**
     * @param $data
     * @return mixed $id on single image success, bool false on fail, bool true on image array add
     */
    public function addImage($data) {
        if ($data['id']) {
            return ($this->imagesTable->add($data));
        }
        if ($data[0]['id']){
            foreach ($data as $image){
                $this->imagesTable->add($image);
            }
            return true;
        }
        else {return false;}
    }


/* update ********************************* */
    /**
     * @param $data array of album information
     * @return mixed $id on success or bool false on fail
     */
    public function updateAlbum($data) {
        return $this->albumsTable->change($data['id'], $data);
    }

    /**
     * Update Image or Images
     * @param $data array with image data or array of images ([0]=> image data, [1] => image data
     * 
     * @return mixed $id on success or bool false on fail
     */
    public function updateImage($data) {
        if ($data['id']) {
            return ($this->imagesTable->change($data['id'], $data));
        }
        if ($data[0]['id']){
            foreach ($data as $image){
                $this->imagesTable->change($image['id'], $image);
            }
            return true;
        }
        else {return false;}
    }

/* delete ********************************* */
    /**
     * @param $id
     * @return bool true on success
     */
    public function deleteWholeAlbum($id) {
        $this->deleteAllAlbumImages($id);
        $this->albumsTable->remove($id);
        return true;
    }

    /**
     * @param $image_id
     * @return bool true on success or false on fail
     */
    public function deleteImage($image_id) {
        $this->imagesTable->remove($image_id);
        return $this->albumImagesTable->removeByImageID($image_id);
    }

    /**
     * @param $id
     * @return bool true on success or false on fail
     */
    public function deleteAllAlbumImages($id) {
        $images = $this->imagesTable->getImagesByAlbumID($id);
        $this->albumImagesTable->removeByAlbumID($id);
        foreach ($images as $image){
            $this->imagesTable->remove ($image['id']);
        }
        return true;
    }
    
    public function storeAlbum ($album_data){

        $album_data = $this->exchangeDataArray($album_data);

        if ($album_data['id'] == '' ){
            $this->addAlbum($album_data);
        }
        else {
            $this->updateAlbum($album_data);
        }
    }

    private function exchangeDataArray($data){
        $int_array = array (
            'id', 'timestamp', 'visibility', 'album_id', 'image_id');
        $exclude_array = array (
            'validators','submit','date');
        $new_data = array();
        foreach ($data as $key => $value){
            if (in_array($key, $int_array)){
                $value = (int) $value;
            }

            if (!in_array($key, $exclude_array)) {
                $new_data[$key] = $value;
            }
        }
        return $new_data;
    }
}