<?php

namespace Cast\View;


use Zend\View\Helper\AbstractHelper;

const TEST_BLAZON = true; // cleanFix for testing

class BlazonHelper extends AbstractHelper
{
    private $sm; // if sth is needed
    // $blazons needs to be stored in db
    private $blazons = array(
                            0 => array ( 'url' => '/img/fry1.png' ),
                            1 => array ( 'url' => '/img/default.png'),
        );
    // $jobs needs to be stored in db and created automatically
    private $jobs = array ('soldat' => '/img/swords.png'); // job strings

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
            if ($overlay2 !== null) {
                bdump( $dumpMsg . ' of $overlay2');
                $overlay2 = 1;
            }
        }

        if (! is_string($overlay1) || $overlay1 == '') $overlay1 = 0;
        if (! is_int($overlay2) ) $overlay2 = 0;
        return $this->createBlazon($baseId, $overlay1, $overlay2, $class);
    }

    public function blazonFamily($familyId, $class = null)
    {
        return $this->blazon($familyId, '', null, $class);
    }
    public function blazonFollowers($familyId, $job = '', $class = null)
    {
        $followersBlazonId = 0; // todo check out witch id the common Blazon has
        return $this->blazon($followersBlazonId, $job, $familyId, $class);
    }

    private function createBlazon($baseId, $job, $familyId, $class = null)
    {
        if ($class !== null){
            $class = "class='$class'";
        } else {
            $class = '';
        }
        $imgPath = '/temp/'. $baseId.$job.$familyId .'.png';
        // check if combination exists
        if (file_exists($imgPath)) return '<img ' . $class . 'src="' . $imgPath . '" >';
        // validate
        $baseId = $this->validateIds($baseId);
        $job    = $this->validateJob($job);
        $familyId = $this->validateIds($familyId);

        return $this->createOverlayOutput($baseId,$job, $familyId, $class, $imgPath);


    }

    private function createOverlayOutput($baseId, $job, $familyId, $class, $imgPath)
    {
        $imgPre = '<img ' . $class . 'src="';
        $imgPost = '" >';
        $newheight = 0;
        $newwidth = 0;
        $newPic = '/public' . $imgPath;
        $path = getcwd();
        $pathPub = $path . '/public';

        $blazonBaseUrl = $blazonOverlay1Url = $blazonOverlay2Url = false;
        // case: family blazon
        if ($job == 0 && $familyId == 0) return $imgPre . $this->blazons[$baseId]['url'] . $imgPost;
        // case: modified blazon
        $blazonBaseUrl = $pathPub . $this->blazons[$baseId]['url'];
        if ($job !== 0)
        {
            $blazonOverlay1Url = $pathPub . $this->blazons[$familyId]['url'];
        }
        if ($familyId !== 0)
        {
            $blazonOverlay2Url = $pathPub . $this->blazons[$familyId]['url'];
        }

        // todo return Blazon = $baseId + family overlay = $family
        $blazonBase = imagecreatefrompng($blazonBaseUrl);
        list($baseWidth, $baseHeight) = getimagesize($blazonBaseUrl);
        if ($blazonOverlay1Url) {
            $blazonOverlay1 = imagecreatefrompng($blazonOverlay1Url);
            list($over1Width, $over1Height) = getimagesize($blazonOverlay1Url);
        }
        if ($blazonOverlay2Url) {
            $blazonOverlay2 = imagecreatefrompng($blazonOverlay2Url);
            list($over2Width, $over2Height) = getimagesize($blazonOverlay2Url);
        }
        // cleanFix untill size is given
        $newheight = $baseHeight;
        $newwidth = $baseWidth;

        $out = imagecreatetruecolor($newwidth, $newheight);
        if ($blazonOverlay1Url) imagecopyresampled($out, $blazonBase, 0, 0, 0, 0, $newwidth, $newheight, $over1Width, $over1Width);
        if ($blazonOverlay2Url) imagecopyresampled($out, $blazonOverlay2, 0, 0, 0, 0, ($newwidth/2), ($newheight/2), $over2Width, $over2Height);
        imagepng($out, $path . $newPic, 5);
        return $imgPre . $imgPath . $imgPost;

    }

    private function validateIds($checkId)
    {
        if (!isset($this->blazons[$checkId])) return 0;
        return $checkId;
    }

    private function validateJob($job)
    {
        if ( $job == 0 ) return 0;
        if (! in_array( $job , $this->jobs ) ) return 0;
        return $job;
    }
}