<?php
namespace Album\Service;


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

    public function updateAlbum($data) {
        
    }

    public function updateImage($data) {}
    
    
    public function addAlbum($data) {
        
    }
    
    public function addImage($data) {}
    
    public function deleteWholeAlbum($id) {
        $this->deleteAllAlbumImages($id);
        $this->albumsTable->remove($id);
    }
    
    public function deleteImage($image_id) {
        $this->imagesTable->remove($image_id);
        $this->albumImagesTable->removeByImageID($image_id);
    }
    
    public function deleteAllAlbumImages($id) {
        $images = $this->imagesTable->getImagesByAlbumID($id);
        $this->albumImagesTable->removeByAlbumID($id);
        foreach ($images as $image){
            $this->imagesTable->remove ($image['id']);
        }
    }


















    private $db;
    private $db_images;

    private $dataPath;

    private $galleries = array ();
    private $images;

    function __construct2($sm)
    {

        $rootPath = getcwd();
        $this->dataPath = $rootPath.'\Data\gallery\\';

        $this->db = $sm->get('Album\Model\AlbumTable'); //@todo db request
        $data = $this->readDBAlbums(); //@todo db request

        $this->mappingAlbums($data); // das hatten wir ja hier schon bis hier


        // $this->db_images = $sm->get('Album\Model\AlbumTable');  //@todo db request
        $this->images = $this->readDBImages();

        echo '<pre>';
        echo 'galleries <br>';
        var_dump($this->galleries);
        echo 'images <br>';
        var_dump($this->images);
        echo '</pre>'; die;
    }


    /**
     * Mapps folder names on db entrys
     *
     * checks if db is up to date
     *
     * @return array of matched db-folder links
     */
    public function getAllGalleries ()
    {
        return $this->galleries;
    }

    /**
     * get all image data from all galleries
     *
     * @return mixed array of images
     */
    public function getAllGalleryImages() {
        return $this->images;
    }

    /** get all data of gallery with id= $id
     *
     * @param $id
     * @return array array with ['gallery'] = array of gallery data; and ['images'] = array of image data
     */
    public function getGalleryDataSetByID ($id){
        $return_set = array ();
        $gallery_data= $this->galleries[$id];
        $images_data = $this->getImagesByGalleryID($id);
        $return_set['gallery'] = $gallery_data;
        $return_set['images'] = $images_data;
        return $return_set;
    }

    /**
     * mapping function
     * @param $data array of e.g. db results
     *
     * sets $this->galleries
     */
    private function mappingAlbums ($data)
    {
        foreach ($data as $id)
        {
            if (!is_dir($this->dataPath.$id['folder'])) continue;
            $this->galleries[$id['id']] = $id;
        }
    }

    /**
     * reads data from db and returns  array with
     * [0] == folders by id; [1] folder name hash
     */
    private function readDBAlbums()
    {
        $dummy = 'on';
        if ($dummy == 'on') {
            $results = $this->createDummy('albums');
        } else {
            $results = $this->db->fetchAllGalleryFolder();
        }

        $folders = array();
        foreach ($results as $row) {
            $id =  $row['id'];
            $folders[$id] = $row;
        }
        return $folders;
    }

    private function readDBImages () {
        $dummy = 'on';
        if ($dummy == 'on') {
            return $this->createDummy('images');
        } else {
            return $this->db->fetchAllImages();
        }
    }

    private function getImagesByGalleryID ($id) {
        $result = array ();
        foreach ($this->images as $image)
            if ($image['gallery_id'] == $id) {
                array_push($result, $image);
            }
        return $result;
    }

    /**
     * @param $for 'albums' or 'images'
     * @return array array of dummy data
     */
    private function createDummy ($for) {
        if ($for == 'albums') {
            return array ( 0 => array('id' => 1, 'folder' => '2016', 'visibility' => 1, 'event' => 'Testevent', 'timestamp' => 76742668 ));
        }
        else if ($for == 'images') {
            return array ( 0 => array('id' => 1, 'gallery_id' => 1, 'name' => '2016', 'extension' => '.jpg','visibility' => 1, 'text' => 'blabla' ));
        }
    }
}