<?php

namespace Cast\View;


use Cast\Service\BlazonService;
use Zend\View\Helper\AbstractHelper;

class BlazonHelper extends AbstractHelper
{
    /** @var  BlazonService */
    private $service;

    function __construct($sm)
    {
        $parentLocator = $sm->getServiceLocator();
        $this->service = $parentLocator->get('BlazonService');
    }

    /**
     * @param mixed $baseBlazon name or id
     * @param mixed $overlay1 name or id
     * @param mixed $overlay2 name or id
     * @return string HTML-string
     */
    public function blazon ($baseBlazon = 'standard', $overlay1 = null, $overlay2 = null)
    {
        $base = ($overlay1 === null && $overlay2 === null)? $this->getBlazonData($baseBlazon, 'big') : $this->getBlazonData($baseBlazon);
        $return = '<div class="blazon">';
        $return .= '<img class="blazon-sub-0" src="' . $base . '" >';
        if( !($overlay1 === null) ) {
            $return .= '<img class="blazon-sub-1" src="' . $this->getBlazonData($overlay1) . '" >';
        }
        if( !($overlay2 === null) ) {
            if ( is_array($overlay2) ){
                $c = 1;
                foreach ($overlay2 as $blazon){
                    $return .= '<img class="blazon-sub-2 sub-' . $c . '" src="' . $this->getBlazonData($blazon) . '" >';
                }
            } else {
                $return .= '<img class="blazon-sub-2" src="' . $this->getBlazonData($overlay2) . '" >';
            }
        }
        $return .= '</div>';
        return $return;
    }

    public function getSingleBlazon($name, $size = 'small')
    {
        $base = ($size == 'small')? $this->getBlazonData($name) : $this->getBlazonData($name, 'big');
        $return = '<div class="blazon">';
        $return .= '<img class="blazon-sub-0" src="' . $base . '" >';
        $return .= '</div>';
        return $return;
    }
    
    private function getBlazonData( $selector, $size = 'small' )
    {
        $picUrl = ($size == 'big') ? $this->service->getBigBlazonUrl($selector) : $this->service->getBlazonUrl($selector);
        return $picUrl;
    }
}