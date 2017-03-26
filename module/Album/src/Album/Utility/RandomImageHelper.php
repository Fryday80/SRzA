<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 27.11.2016
 * Time: 00:26
 */

namespace Album\Utility;


use Album\Service\GalleryService;
use Zarganwar\PerformancePanel\Register;
use Zend\View\Helper\AbstractHelper;

/**
 * Class RandomImageHelper <br>
 * creates 3 random images
 *
 * @package Album\Utility
 */
class RandomImageHelper extends AbstractHelper
{
    /**
     * @var GalleryService
     */
    private $galleryService;
    private $result;


    function __construct($galleryService)
    {
        $this->galleryService = $galleryService;
        $this->createRandoms();
    }

    function createRandoms()
    {
        Register::add("create randoms");
        $this->result = $this->galleryService->getRandomImage($count = 3);
        Register::add("after created randoms");
    }

    function scroller()
    {
        Register::add("start scroller");
        $id = 'S_'.uniqid();
        $return = '
            <style>
                .simple-slide-show {
                    position: relative;
                    overflow: hidden;
                    width: 100%;
                    height: 100%;
                }
                .simple-slide-show img {
                    position: absolute;
                    top: 0px;
                    left: 0px;
                    z-index: 1;
                }
                .simple-slide-show img.active {
                    z-index: 3;
                }
            </style>
        ';
        $return .= '  <div id="'.$id.'" >';
        foreach ($this->result as $picture)
        {
            $return .= '<img src="' . $picture->livePath . '">';
        }
        $return .= '</div><script>
                            $("#'.$id.'").simpleSlideShow({
                                autoMode: "loop", 
                                customClass: "vert",
                                orientation: "vertical",
                                frameRate: 20,
                                speed: 3
                            });
                    </script>';
        return $return;
    }
}