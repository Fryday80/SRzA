<?php
namespace Album\Service;
use vakata\database\Exception;


/**
 * Class GalleryService
 *
 * Manages the db <-> folder mapping
 * for additional (db) information
 */
Class GalleryService
{
    private $albumsTable;
    private $albumImagesTable;
    private $imagesTable;



    function __construct($sm)
    {
        $this->albumsTable = $sm->get('Album\Model\AlbumsTable');
        $this->albumImagesTable = $sm->get('Album\Model\AlbumImagesTable');
        $this->imagesTable = $sm->get('Album\Model\ImagesTable');
    }

/* read ******************************** */
    public function getAllAlbums() {
        return $this->albumsTable->fetchAllAlbums();
    }

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
    public function addAlbum($data) {
        return $this->albumsTable->add($data);
    }
    
    public function addImage($data) {
        if ($data['id']) {
            return ($this->imagesTable->add($data));
        }
        if ($data[0]['id']){
            foreach ($data as $image){
                $this->imagesTable->add($image);
            }
        }
        else {return false;}
    }


/* update ********************************* */
    /**
     * @param $data array of album information
     * @return mixed $id at success or false at fail
     */
    public function updateAlbum($data) {
        return $this->albumsTable->change($data['id'], $data);
    }

    /**
     * Update Image or Images
     * @param $data array with image data or array of images ([0]=> image data, [1] => image data
     * 
     * @return mixed $id at success or false at fail
     */
    public function updateImage($data) {
        if ($data['id']) {
            return ($this->imagesTable->change($data['id'], $data));
        }
        if ($data[0]['id']){
            foreach ($data as $image){
                $this->imagesTable->change($image['id'], $image);
            }
        }
        else {return false;}
    }

/* delete ********************************* */
    public function deleteWholeAlbum($id) {
        $this->deleteAllAlbumImages($id);
        $this->albumsTable->remove($id);
    }
    
    public function deleteImage($image_id) {
        $this->imagesTable->remove($image_id);
        return $this->albumImagesTable->removeByImageID($image_id);
    }
    
    public function deleteAllAlbumImages($id) {
        $images = $this->imagesTable->getImagesByAlbumID($id);
        $this->albumImagesTable->removeByAlbumID($id);
        foreach ($images as $image){
            $this->imagesTable->remove ($image['id']);
        }
    }

    private function exchangeDataArray($data){
        if (!isset($data['aid']) && !isset($data['id']))
        {
            throw new Exception ('ids missing!!');
        }

        $new_data = array(
            'albums' => array(
                'id' => $data['aid'] ?: Null,
                'folder' => $data['folder'] ?: Null,
                'event' => $data['event'] ?: Null,
                'timestamp' => $data['timestamp'] ?: Null,
                'preview_pic' => $data['preview_pic'] ?: Null,
                'visibility' => $data['avisibility'] ?: 0
            ),
            'albumimage' => array(
                'album_id' => $data['aid'] ?: Null,
                'image_id' => $data['id'] ?: Null,
            ),
            'images' => array(
                'id' => $data['id'],
                'filename' => $data['filename'] ?: Null,
                'extension' => $data['extension'] ?: Null,
                'text_1' => $data['text_1'] ?: Null,
                'text_2' => $data['text_2'] ?: Null,
                'visibility' => $data['visibility'] ?: 0
            ));

        return $new_data;
    }
}