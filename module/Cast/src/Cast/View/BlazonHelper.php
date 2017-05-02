<?php

namespace Cast\View;


use Zend\View\Helper\AbstractHelper;

const TEST_BLAZON = false; // cleanFix for testing

class BlazonHelper extends AbstractHelper
{
    // if sth is needed
    private $sm;

    // $blazons needs to be stored in db
    private $blazons = array(
                            0 => array ( 'url' => '/img/blazons/shield-tross.big.png' ),
                            1 => array ( 'url' => '/img/blazons/zuLeym.png' ),
                            2 => array ( 'url' => '/img/blazons/fryschild.png'),
    );

    // $jobs needs to be stored in db and created automatically
    private $jobs = array (
                        'soldat' => '/img/blazons/swords.png'
    ); // job strings

    private $options;
    private $activeOption;

    function __construct($sm)
    {
        $this->sm = $sm;
        $this->standards();
        $this->activeOption = $this->options['small'];
    }

    public function setSize($size)
    {
        if( is_int($size) || is_float($size) ){
            $this->customizedSize($size);
        } else {
            if (key_exists($size, $this->options)) {
                $this->activeOption = $this->options[$size];
            } else {
                $this->activeOption = $this->options['small'];
            }
        }
    }

    public function blazon($baseId, $overlay1 = null, $overlay2 = null, $class = null)
    {
        // cleanFix for testing
        if (TEST_BLAZON) {
            $dumpMsg = 'BlazonHelper.php test override';
            if ($baseId !== 0) {
                bdump( $dumpMsg . ' of $baseId');
                $baseId = 0;
            }
            if ($overlay1 !== null) {
                bdump( $dumpMsg . ' of $overlay1');
                $overlay1 = 'soldat';
            }
            if ($overlay2 !== null) {
                bdump( $dumpMsg . ' of $overlay2');
                $overlay2 = 2;
            }
        }

        if ( !is_string($overlay1) ) $overlay1 = null;
        if ( !is_int($overlay2) ) $overlay2 = null;

        return $this->createBlazon($baseId, $overlay1, $overlay2, $class);
    }

    public function blazonFamily($familyId, $class = null)
    {
        $class .= 'family';
        return $this->blazon($familyId, '', null, $class);
    }

    public function blazonFollowers($familyId, $job = '', $class = null)
    {
        $class .= 'followers';
        $followersBlazonId = 0; // todo check out witch id the common Blazon has
        return $this->blazon($followersBlazonId, $job, $familyId, $class);
    }

    private function createBlazon($baseId, $job = null, $familyId = null, $class = null)
    {
        $class = ($class == null) ? 'x'.$job.$familyId : $baseId.$job.$familyId . ' ' . $class;

        $baseId = $this->validateIds($baseId);
        $job    = $this->validateJob($job);
        $familyId = $this->validateIds($familyId);
        
        $img = $this->createImg($baseId, $job, $familyId);
        return '<div class="blazon '. $class .  '" style = " position: relative; '. $this->activeOption['divStyle'] . '">' . $img. '</div>';
    }

    private function createImg($baseId, $job, $familyId)
    {
        $backgroundImage2 = $backgroundImage3 = '';
        
        $backgroundImage1 = "<img src = '" . $this->blazons[$baseId]['url'] . "' style=' position: relative; z-index: 1;". $this->activeOption['height1'] ."'>";
        if ( isset($this->jobs[$job]) ){
            $backgroundImage2 = "<img src = '".  $this->jobs[$job] . "' style=' position: absolute; z-index: 2; ". $this->activeOption['overlay1'] ."'>";
        }
        if ( isset($this->blazons[$familyId]['url']) ){
            $backgroundImage3 =  "<img src = '" . $this->blazons[$familyId]['url'] . "' style=' position: absolute; z-index: 3; ". $this->activeOption['overlay2'] ."'>";
        }
        return $backgroundImage1.$backgroundImage2.$backgroundImage3;
    }

    private function validateIds($checkId)
    {
        if ( isset($this->blazons[$checkId]) ) return $checkId;
        return null;
    }

    private function validateJob($job)
    {
        if ( (!is_string($job)) || (!key_exists( $job , $this->jobs )) ) return null;
        return $job;
    }

    private function standards()
    {
        $height = floatval(200);
        $top1    = $height*0.155;
        $height1 = $height*0.435;
        $height2 = $height*0.45;

        $this->options = array(
            "big" => array(
                "divStyle" => "height: " . $height . "px; width: " . $height . "px;",
                "height1"  => "height: " . $height . "px;",
                "overlay1" => "left:0; right:0; margin: 0 auto; top: " . $top1 . "px; height: " . $height1 . "px;",
                "overlay2" => "bottom: 0;  left:0; right:0; margin: 0 auto; height: " . $height2 . "px;",
            ),
            "small" => array(
                "divStyle" => "height: " . ($height /4) . "px; width: " . ($height /4) . "px;",
                "height1"  => "height: " . ($height /4) . "px;",
                "overlay1" => "left:0; right:0; margin: 0 auto; top: " . ($top1 /4) . "px; height: " . ($height1 /4) . "px;",
                "overlay2" => "bottom: 0; left:0; right:0; margin: 0 auto; height: " . ($height2 /4) . "px;",
            ),
        );
    }

    private function customizedSize($size)
    {
        $height = floatval(200);
        $top1    = $height*31/200;
        $height1 = $height*87/200;
        $height2 = $height*90/200;
        
        $this->activeOption = array(
            "divStyle" => "height: " . ($height /$size) . "px; width: " . ($height /$size) . "px;",
            "height1"  => "height: " . ($height /$size) . "px;",
            "overlay1" => "left:0; right:0; margin: 0 auto; top: " . ($top1 /$size) . "px; height: " . ($height1 /$size) . "px;",
            "overlay2" => "bottom: 0;  left:0; right:0; margin: 0 auto; height: " . ($height2 /$size) . "px;",
        );
    }
}