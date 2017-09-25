<?php
namespace Cast\View\Helper;

use Cast\Service\BlazonService;
use Zend\View\Helper\AbstractHelper;

class BlazonHelper extends AbstractHelper
{
    const BLAZON_PATH = '/media/file/';

    /** @var  BlazonService */
    private $service;

    function __construct(BlazonService $service) {
        $this->service = $service;
    }

    public function blazon($arguments = null, $familyBlazon = false) {
        if ($arguments == null) $arguments = array(1,0,0);
        if (isset($arguments['name'])) $arguments = $this->service->getArgumentsByChar($arguments, $familyBlazon);
        $arguments = $this->service->getFilenames($arguments, $familyBlazon);

        return $this->createBlazonHTML($arguments);
    }

    private function createBlazonHTML($arguments) {
        $html  = '<div class="blazon">';
        for ($i = 0; $i < 3; $i++) {
            if ($arguments[$i] == '') continue;
            $html .= "<img class='blazon-sub-$i' src='" . self::BLAZON_PATH . $arguments[$i] . "' >";
        }
        $html .= '</div>';

        return $html;
    }
}