<?php

namespace Cast\View;


use Zend\View\Helper\AbstractHelper;

const TEST_BLAZON = true; // cleanFix for testing

class BlazonHelper extends AbstractHelper
{
    private $sm; // if sth is needed
    private $blazons = array( // example to code more concrete
                            0 => array ( 'url' => '/img/fry1.png' ),
                            1 => array ( 'url' => '/img/default.png'),
        );
    private $jobs = array (); // job strings

    function __construct($sm)
    {
        $this->sm = $sm;
    }

    public function blazon($baseId, $overlay1 = '', $overlay2 = null)
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
        return $this->createBlazon($baseId, $overlay1, $overlay2);
    }

    public function blazonFamily($familyId)
    {
        return $this->blazon($familyId);
    }
    public function blazonFollowers($familyId, $job = '')
    {
        $followersBlazonId = 0; // todo check out witch id the common Blazon has
        return $this->blazon($followersBlazonId, $job, $familyId);
    }

    private function createBlazon($baseId, $job, $familyId)
    {
        $newheight = 0;
        $newwidth = 0;
        $callPath = '/temp/'. uniqid() .'.png';
        $newPic = '/public' . $callPath;
        $imgPre = '<img src="';
        $imgPost = '" >';
        $baseId = $this->validateIds($baseId);
        $job    = $this->validateJob($job);
        $familyId = $this->validateIds($familyId);

        bdump(($job == 0 && $familyId == 0));
        bdump(($job == 0 && $familyId !== 0));
        bdump(($job !== 0 && $familyId == 0));
        if ($job == 0 && $familyId == 0)
        {
            // todo return Blazon = $baseId
            return $imgPre . $this->blazons[$baseId]['url'] . $imgPost;
        }
        if ($job == 0 && $familyId !== 0){
            $path = getcwd();
            $pathPub = $path . '/public';
            $blazonBaseUrl = $pathPub . $this->blazons[$baseId]['url'];
            $blazonOverlay1Url = $pathPub . $this->blazons[$familyId]['url'];
            // todo return Blazon = $baseId + family overlay = $family
            $blazonBase = imagecreatefrompng($blazonBaseUrl);
            $blazonOverlay1 = imagecreatefrompng($blazonOverlay1Url);

            list($baseWidth, $baseHeight) = getimagesize($blazonBaseUrl);
            list($over1Width, $over1Height) = getimagesize($blazonOverlay1Url);

            $newheight = $baseHeight;
            $newwidth = $baseWidth;

            $out = imagecreatetruecolor($newwidth, $newheight);
            imagecopyresampled($out, $blazonBase, 0, 0, 0, 0, $newwidth, $newheight, $baseWidth, $baseHeight);
            imagecopyresampled($out, $blazonOverlay1, 0, 0, 0, 0, $newwidth, $newheight, $over1Width, $over1Height);
            imagepng($out, $path . $newPic, 5);
            return $imgPre . $callPath . $imgPost;
        }
        if ($job !== 0 && $familyId == 0){
            // todo return Blazon = $baseId + family overlay = $job
        }
        else {
            // todo return Blazon = $baseId + job overlay = $job + family overlay = $family
        }
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