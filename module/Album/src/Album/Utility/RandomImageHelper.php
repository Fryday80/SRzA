<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 27.11.2016
 * Time: 00:26
 */

namespace Album\Utility;


use Album\Service\GalleryService;

/**
 * Class RandomImageHelper <br>
 * creates 3 random images
 *
 * @package Album\Utility
 */
class RandomImageHelper
{
    /**
     * @var GalleryService
     */
    private $galleryService;

    public $randomPic1;
    public $randomPic2;
    public $randomPic3;


    function __construct($galleryService)
    {
        $this->galleryService = $galleryService;
        $this->createRandoms();
    }

    function createRandoms(){
        $allAlbums = $this->galleryService->getAllAlbums();
        $allPics = array();
        foreach ($allAlbums as $album){
            //todo only guest pics
            array_push($allPics, $album->loadImages());
        }
        $pics = count($allPics);
        $randomPics = array_rand ($pics, 3 );
        $this->randomPic1 = $randomPics[0];
        $this->randomPic2 = $randomPics[1];
        $this->randomPic3 = $randomPics[2];
    }
}