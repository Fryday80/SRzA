<?php

namespace Cast\View;


use Zend\View\Helper\AbstractHelper;

class BlazonHelper extends AbstractHelper
{
    // if sth is needed
    private $sm;
    // data
    private $blazons;
    private $jobs;
    private $preSetFactors;

    private $activeOption;

    function __construct($sm)
    {
        $this->sm = $sm;
        $this->setStandards();
        $this->setBlazonsFromDB();
        $this->setJobsFromDB();
        $this->customizedSize( $this->preSetFactors['small'] );
    }

    /** sets blazons size as factor of 1px       <br>
     * or sets size to small if keyword does not fit
     * @param mixed $size int|float  or keyword
     * @return bool returns true       <br>
     * or returns false if keyword does not fit
     */
    public function setSize($size)
    {
        if( is_int($size) || is_float($size) )
        {
            $this->customizedSize($size);
            return true;
        }
        if ( key_exists($size, $this->preSetFactors) )
        {
            $this->customizedSize( $this->preSetFactors[$size] );
            return true;
        }
        $this->customizedSize( $this->preSetFactors['small'] );
        return false;
    }

    /** returns HTML string for blazon
     * @param int $baseBlazon basic blazon ID
     * @param string $job job string
     * @param int $guardiansFamilyId guardians family ID
     * @param string $class additional classes for wrapping div
     * @return string HTML string
     */
    public function blazon($baseBlazon, $job = null, $guardiansFamilyId = null, $class = null)
    {
        if ( !is_string($job) ) $job = null;
        if ( !is_int($guardiansFamilyId) ) $guardiansFamilyId = null;

        return $this->createBlazonDiv($baseBlazon, $job, $guardiansFamilyId, $class);
    }

    /** returns HTML string for family blazon
     * @param int $familyId
     * @param string $class
     * @return string HTML string
     */
    public function blazonFamily($familyId, $class = null)
    {
        $class .= 'family';
        return $this->blazon($familyId, '', null, $class);
    }

    /** returns HTML string for followers blazon
     * @param int $familyId
     * @param string $job
     * @param string $class
     * @return string HTML string
     */
    public function blazonFollowers($familyId, $job = '', $class = null)
    {
        $class .= 'followers';
        $followersBlazonId = 0; // todo check out witch id the common Blazon has
        return $this->blazon($followersBlazonId, $job, $familyId, $class);
    }

    private function createBlazonDiv($baseId, $job = null, $familyId = null, $class = null)
    {
        $class = ($class == null) ? 'x'.$job.$familyId : $baseId.$job.$familyId . ' ' . $class;

        $baseId = $this->validateIds($baseId);
        $job    = $this->validateJob($job);
        $familyId = $this->validateIds($familyId);
        
        $img = $this->createImgElements($baseId, $job, $familyId);
        return '<div class="blazon '. $class .  '" style = " position: relative; border-radius: 50%; '. $this->activeOption['divStyle'] . '">' . $img. '</div>';
    }

    private function createImgElements($baseId, $job, $familyId)
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

    private function customizedSize($size)
    {
        $height  = floatval(1)*$size;
        $left    = $height * 0.03;
        $top1    = $height * 0.155;
        $height1 = $height * 0.435;
        $height2 = $height * 0.45;

        $this->activeOption = array(
            "divStyle" => "height: " . ($height) . "px; width: " . ($height) . "px;",
            "height1"  => "height: " . ($height) . "px;",
            "overlay1" => "left: " . ($left) . "px; right: 0; margin: 0 auto; top: " . ($top1) . "px; height: " . ($height1) . "px;",
            "overlay2" => "bottom: 0;  left: " . ($left) . "px; right: 0; margin: 0 auto; height: " . ($height2) . "px;",
        );
    }

    private function setStandards()
    {
        $this->preSetFactors = array(
            "big" => 200, // size used to design
            "small" => 50,
        );
    }

    private function setJobsFromDB()
    {
        //todo read from db
        $this->jobs = array (
            'soldat' => '/img/blazons/swords.png'
        );
    }

    private function setBlazonsFromDB()
    {
        //todo read from db
        $this->blazons = array(
            0 => array ( 'url' => '/img/blazons/shield-tross.big.png' ),
            1 => array ( 'url' => '/img/blazons/zuLeym.png' ),
            2 => array ( 'url' => '/img/blazons/fryschild.png'),
        );
    }
}