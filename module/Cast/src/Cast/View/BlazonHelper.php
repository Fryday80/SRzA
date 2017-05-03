<?php

namespace Cast\View;


use Zend\View\Helper\AbstractHelper;

class BlazonHelper extends AbstractHelper
{
    // if sth is needed
    private $sm;

    function __construct($sm)
    {
        $this->sm = $sm;
    }
    public function blazon ($baseBlazon, $overlay1 = null, $overlay2 = null){
        $return = '<div class="blazon">';
        $return .= '<img class="blazon-sub-0" src="' . $this->getPictureUrl($baseBlazon) . '" >';
        if( !($overlay1 === null) ) {
            $return .= '<img class="blazon-sub-1" src="' . $this->getPictureUrl($overlay1) . '" >';
        }
        if( !($overlay2 === null) ) {
            if ( is_array($overlay2) ){
                $c = 1;
                foreach ($overlay2 as $blazon){
                    $return .= '<img class="blazon-sub-2 sub-' . $c . '" src="' . $this->getPictureUrl($blazon) . '" >';
                }
            } else {
                $return .= '<img class="blazon-sub-2" src="' . $this->getPictureUrl($overlay2) . '" >';
            }
        }
        $return .= '</div>';
        return $return;
    }
    private function getPictureUrl( $selector )
    {
        if (is_string( $selector )) {
            //todo get pic-url by string from where ever
            $picUrl = '/img/blazons/swords.png';
            return $picUrl;
        }
        if (is_int( $selector )) {
            //todo get pic-url by ID from where ever
            // test dummy data
            if ($selector === 0){
                $picUrl = '/img/blazons/shield-tross.big.png';
                return $picUrl;
            }
            elseif ($selector == 1){
                $picUrl = '/img/blazons/zuLeym.png';
                return $picUrl;
            }
            else {
                $picUrl = '/img/blazons/fryschild.png';
                return $picUrl;
            }
        }
    } // das sind dummy daten
}