<?php
namespace Cast\View\Helper;

use Cast\Service\BlazonService;
use Zend\View\Helper\AbstractHelper;

class BlazonHelper extends AbstractHelper
{
    const BLAZON_PATH = '/media/file/wappen/';
    /** @var  BlazonService */
    private $service;
    private $parentBlazons = false;

    function __construct(BlazonService $service)
    {
        $this->service = $service;
    }

    public function blazon($arguments = null, $familyBlazon = false)
    {
        if ($arguments == null) $arguments = array(1,0,0);
        if (isset($arguments['name'])) $arguments = $this->service->getArgumentsByChar($arguments, $familyBlazon);
        $arguments = $this->service->getHTMLArguments($arguments, $familyBlazon);
        
        return $this->blazonHTML($arguments);
    }

    private function blazonHTML($arguments)
    {
        $html  = '<div class="blazon">';
        $html .= '<img class="blazon-sub-0" src="' . self::BLAZON_PATH . $arguments[0] . '" >';
        if($arguments[1] !== '')
            $html .= '<img class="blazon-sub-1" src="' . self::BLAZON_PATH . $arguments[1] . '" >';
        if($arguments[2] !== '')
            $html .= '<img class="blazon-sub-2" src="' . self::BLAZON_PATH . $arguments[2] . '" >';
        $html .= '</div>';
        
        return $html;
    }
}