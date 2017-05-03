<?php

namespace Cast\View;


use Zend\View\Helper\AbstractHelper;

class BlazonHelper extends AbstractHelper
{
    // if sth is needed
    private $sm;
    // data
    private $preSetFactors;

    private $activeOption;

    function __construct($sm)
    {
        $this->sm = $sm;
    }
    public function blazon ($baseBlazon, $overlay1 = null, $overlay2 = null){
        $return = $this->getStyle();
        $return .= '<div class="blazon">';
        $return .= '<img class="blazon-sub-0" src="' . $this->getPictureUrl($baseBlazon) . '" >';
        if( !($overlay1 === null) ) {
            $return .= '<img class="blazon-sub-1" src="' . $this->getPictureUrl($overlay1) . '" >';
        }
        if( !($overlay2 === null) ) {
            $return .= '<img class="blazon-sub-2" src="' . $this->getPictureUrl($overlay2) . '" >';
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
    }

    private function getStyle(){
        return '<style>
                       .blazon{ position: relative;} 
                       .blazon-sub0{ position: relative; z-index: 1}
                       .blazon-sub1{ position: absolute; z-index: 2 left: 0; right: 0; margin: 0 auto;}
                       .blazon-sub2{ position: absolute; z-index: 3 left: 0; right: 0; margin: 0 auto;}
                </style>';
    }

//    function __construct($sm)
//    {
//        $this->sm = $sm;
//        $this->preSetFactors = array(
//            "big" => 200, // size used to design
//            "small" => 50,
//        );
//        $this->customizedSize( $this->preSetFactors['small'] );
//    }
//
//
//    /** sets blazons size as factor of 1px       <br>
//     * or sets size to small if keyword does not fit
//     * @param mixed $size int|float  or keyword
//     * @return bool returns true       <br>
//     * or returns false if keyword does not fit
//     */
//    public function setSize($size)
//    {
//        if( is_int($size) || is_float($size) )
//        {
//            $this->customizedSize($size);
//            return true;
//        }
//        if ( key_exists($size, $this->preSetFactors) )
//        {
//            $this->customizedSize( $this->preSetFactors[$size] );
//            return true;
//        }
//        $this->customizedSize( $this->preSetFactors['small'] );
//        return false;
//    }
//
//    /** returns HTML string for blazon
//     * @param int $baseBlazonID basic blazon ID
//     * @param string $overlay1string job string
//     * @param int $overlay2id guardians family ID
//     * @param string $class additional classes for wrapping div
//     * @return string HTML string
//     */
////    public function blazon($baseBlazonID, $overlay1string = null, $overlay2id = null, $class = null)
////    {
////        if ( !is_string($overlay1string) ) $overlay1string = null;
////        if ( !is_int($overlay2id) ) $overlay2id = null;
////
////        return $this->createBlazonDiv($baseBlazonID, $overlay1string, $overlay2id, $class);
////    }
//
//    /** returns HTML string for family blazon
//     * @param int $baseBlazonID
//     * @param string $class
//     * @return string HTML string
//     */
//    public function blazonFamily($baseBlazonID, $class = null)
//    {
//        $class .= 'family';
//        return $this->blazon($baseBlazonID, null, null, $class);
//    }
//
//    /** returns HTML string for followers blazon
//     * @param int $overlay2id
//     * @param string $overlay1string
//     * @param string $class
//     * @return string HTML string
//     */
//    public function blazonFollowers($overlay2id, $overlay1string = null, $class = null)
//    {
//        $class .= 'followers';
//        $standardBlazonId = 0; // todo check out witch id the common Blazon has
//        return $this->blazon($standardBlazonId, $overlay1string, $overlay2id, $class);
//    }
//
//    private function createBlazonDiv($baseBlazonID, $overlay1string = null, $overlay2id = null, $class = null)
//    {
//        $class = ($class == null) ? 'x'.$overlay1string.$overlay2id : 'x'.$overlay1string.$overlay2id . ' ' . $class;
//
//        $baseBlazonID   = ( $baseBlazonID === null )   ? null : $this->getPictureUrl($baseBlazonID);
//        $overlay1string = ( $overlay1string === null ) ? null : $this->getPictureUrl($overlay1string);
//        $overlay2id     = ( $overlay2id === null )     ? null : $this->getPictureUrl($overlay2id);
//
//        $img = $this->createImgElements($baseBlazonID, $overlay1string, $overlay2id);
//        return '<div class="blazon '. $class .  '" style = " position: relative; '. $this->activeOption['divStyle'] . '">' . $img. '</div>';
//    }
//
//    private function createImgElements($baseBlazonIDURL, $overlay1stringURL = null, $overlay2idURL = null)
//    {
//        $backgroundImage2 = $backgroundImage3 = '';
//        $backgroundImage1 = "<img src = '" . $baseBlazonIDURL . "' style=' position: relative; z-index: 1;". $this->activeOption['height1'] ."'>";
//        if ( $overlay1stringURL !== null ){
//            $backgroundImage2 = "<img src = '".  $overlay1stringURL . "' style=' position: absolute; z-index: 2; ". $this->activeOption['overlay1'] ."'>";
//        }
//        if ( $overlay2idURL !== null ){
//            $backgroundImage3 =  "<img src = '" . $overlay2idURL . "' style=' position: absolute; z-index: 3; ". $this->activeOption['overlay2'] ."'>";
//        }
//        return $backgroundImage1.$backgroundImage2.$backgroundImage3;
//    }


//    private function customizedSize($size)
//    {
//        $height  = floatval(1)*$size;
//        $left    = $height * 0.03;
//        $top1    = $height * 0.155;
//        $height1 = $height * 0.435;
//        $height2 = $height * 0.45;
//
//        $this->activeOption = array(
//            "divStyle" => "height: " . ($height) . "px; width: " . ($height) . "px;",
//            "height1"  => "height: " . ($height) . "px;",
//            "overlay1" => "left: " . ($left) . "px; right: 0; margin: 0 auto; top: " . ($top1) . "px; height: " . ($height1) . "px;",
//            "overlay2" => "bottom: 0;  left: " . ($left) . "px; right: 0; margin: 0 auto; height: " . ($height2) . "px;",
//        );
//    }
}