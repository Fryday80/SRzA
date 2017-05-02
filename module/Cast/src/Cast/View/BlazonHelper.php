<?php

namespace Cast\View;


use Zend\View\Helper\AbstractHelper;

const TEST_BLAZON = true; // cleanFix for testing

class BlazonHelper extends AbstractHelper
{
    private $sm; // if sth is needed
    // $blazons needs to be stored in db
    private $blazons = array(
                            0 => array ( 'url' => '/img/blazons/shield-tross.big.png' ),
                            1 => array ( 'url' => '/img/blazons/zuLeym.png' ),
                            2 => array ( 'url' => '/img/blazons/fryschild.png'),
        );
    // $jobs needs to be stored in db and created automatically
    private $jobs = array ('soldat' => '/img/blazons/swords.png'); // job strings

    // options
    private $imgHeight = 200;

    function __construct($sm)
    {
        $this->sm = $sm;
    }

    public function blazon($baseId, $overlay1 = '', $overlay2 = null, $class = null)
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
                $overlay2 = 1;
            }
        }

        if ( (!is_string($overlay1)) || ($overlay1 == '') ) $overlay1 = 0;
        if (! is_int($overlay2) ) $overlay2 = 0;


        return $this->createBlazon($baseId, $overlay1, $overlay2, $class);
    }

    public function blazonFamily($familyId, $class = null)
    {
        $class .= 'family';
        return $this->blazon($familyId, '', null, $class);
    }
    public function blazonFollowers($familyId, $job = '', $class = null)
    {
        $followersBlazonId = 0; // todo check out witch id the common Blazon has
        return $this->blazon($followersBlazonId, $job, $familyId, $class);
    }

    private function createBlazon($baseId, $job, $familyId, $class = null)
    {
        $class = ($class == null) ? 'x'.$job.$familyId : $baseId.$job.$familyId . ' ' . $class;

        $baseId = $this->validateIds($baseId);
        $job    = $this->validateJob($job);
        $familyId = $this->validateIds($familyId);
        
        $img = $this->createImg($baseId, $job, $familyId);
        return '<div class="blazon '. $class .  '" style = " position: relative; height: '. $this->imgHeight .'px;">' . $img. '</div>';


    }

    private function createImg($baseId, $job, $familyId)
    {
        $backgroundImage2 = $backgroundImage3 = '';
        
        $backgroundImage1 = "<img src = '" . $this->blazons[$baseId]['url'] . "' style=' position: absolute; z-index: 3; height: " . $this->imgHeight . "px;'>";
        if ( isset($this->jobs[$job]) ){
            $backgroundImage2 = "<img src = '".  $this->jobs[$job] . "' style=' position: absolute; left: 61px; top: 31px; z-index: 5; height: 87px;'>";
        }
        if ( isset($this->blazons[$familyId]['url']) && $baseId !== $familyId){
            $backgroundImage3 =  "<img src = '" . $this->blazons[$familyId]['url'] . "' style=' position: absolute; bottom: 0; left: 75px; z-index: 7; height: 90px;'>";
        }
        return $backgroundImage1.$backgroundImage2.$backgroundImage3;
    }

    private function validateIds($checkId)
    {
        if (!isset($this->blazons[$checkId])) return 0;
        return $checkId;
    }

    private function validateJob($job)
    {
        if ( !is_string($job) ) return 0;
        if (!key_exists( $job , $this->jobs ) ) return 0;
        return $job;
    }

    private function isHigh($url)
    {
        list($width, $height) = getimagesize($url);
        if ($height > $width) return true;
        return false;
    }
}