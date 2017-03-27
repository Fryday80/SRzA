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
        $this->result = $this->galleryService->getRandomImage($count = 3);
    }

    function scroller()
    {
        $id = 'S_'.uniqid();
        $return = '<link href="/libs/simpleSlideShow/simpleSlideShow.css" rel="stylesheet" type="text/css">';
        $return .= '  <div id="'.$id.'" >';
        foreach ($this->result as $picture)
        {
            $return .= '<img src="' . $picture->livePath . '" >'; //hard code style = "position: absolute; top: 0; left: 0;"
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