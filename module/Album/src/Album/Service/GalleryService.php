<?php
namespace Album\Service;
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 08.11.2016
 * Time: 21:47
 */

/**
 * Class GalleryService
 *
 * Manages the db <-> folder mapping
 * for additional (db) information
 */
Class GalleryService
{
    private $db_album;
    private $db_images;

    private $dataPath;

    private $galleries = array ();
    private $images;

    function __construct($sm)
    {
        $rootPath = getcwd();
        $this->dataPath = $rootPath.'\Data\gallery\\';

        $this->db_album = $sm->get('Album\Model\AlbumTable');

        $this->mapping();

        var_dump($this->galleries); die;

        $this->db_images = $sm->get('Album\Model\ImageTable');
        $this->images = $this->db_images->getAllImages();
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
     * mapping function
     *
     * sets $this->galleries
     */
    private function mapping ()
    {
        $data = $this->read_db();
        foreach ($data as $id)
        {
            if (!is_dir($this->dataPath.$id['folder'])) continue;
            $this->galleries[$id['id']] = $id;
        }
    }

    /* ---------db functions ---------*/ //

    /**
     * reads data from db and returns  array with [0] == folders by id; [1] folder name hash
     */
    private function read_db()
    {
        $results = $this->db->fetchAllGalleryFolder();

        //Dummy Data
        $results = array ( 0 => array('id' => 1, 'folder' => '2016', 'visibility' => 1 ));

        $folders = array();
        foreach ($results as $row) {
            $id =  $row['id'];
            $folder =  $row['folder'];
            $visibility =  $row['visibility'];

            $folders[$id] = array (
                    'id'        => $id,
                    'folder'    => $folder,
                    'visibility'=> $visibility
                );
        }

        return $folders;
    }

    
    
    public function getAllGalleryImages() {
        return $this->images;
    }

    public function getImagesByGalleryID ($id) {
        $result = array ();
        foreach ($this->images as $image)
            if ($image['gallery_id'] == $id) {
                array_push($result, $image);
            }
        return $result;
    }
}