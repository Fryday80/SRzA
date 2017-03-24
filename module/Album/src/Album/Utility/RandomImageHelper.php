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
        $this->randomPic1 = $this->galleryService->getRandomImage($count = 1);
        $this->randomPic2 = $this->galleryService->getRandomImage($count = 1);
        $this->randomPic3 = $this->galleryService->getRandomImage($count = 1);
    }
}