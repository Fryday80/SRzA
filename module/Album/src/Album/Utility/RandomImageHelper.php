<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 27.11.2016
 * Time: 00:26
 */

namespace Album\Utility;


use Album\Service\GalleryService;
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
        $return = '  <ul id="scroller" >';
        foreach ($this->result as $picture)
        {
            $return .= '<li style="text-align: center;">
                            <img src="' . $picture->livePath . '" style="width: 100%; margin-left: auto; margin-right: auto;">
                        </li>';
        }
        $return .= '</ul>
                   <script> $("#scroller").simplyScroll({
                                autoMode: \'loop\', 
                                customClass: \'vert\',
                                orientation: \'vertical\',
                                frameRate: 20,
                                speed: 3
                            });
                    </script>';
        return $return;
    }
}