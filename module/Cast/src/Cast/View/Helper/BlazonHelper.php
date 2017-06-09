<?php
namespace Cast\View\Helper;


use Cast\Service\BlazonService;
use Zend\View\Helper\AbstractHelper;

class BlazonHelper extends AbstractHelper
{
    /** @var  BlazonService */
    private $service;
    private $parentBlazons = false;

    function __construct(BlazonService $service)
    {
        $this->service = $service;
    }

    /**
     * @param array [$baseBlazon = 'standard', $overlay1 = null, $overlay2 = null] <br/>
     * mixed $baseBlazon name or id <br/>
     * mixed $overlay1 name or id <br/>
     * mixed $overlay2 name or id <br/>
     * @return string HTML-string
     */
    public function blazon ($call = array())
    {
        $baseBlazon = (isset($call[0]) && $call[0] !== 0 ) ? $call[0] : 'standard';
        $overlay1 = (isset($call[1]))   ? $call[1] : null;
        $overlay2 = (isset($call[2]))   ? $call[2] : null;
        //cleanfix
//bdump($call);

        $base = ($overlay1 === null && $overlay2 === null)? $this->getBlazonData($baseBlazon, 'big') : $this->getBlazonData($baseBlazon);
        $return = '<div class="blazon">';
        $return .= '<img class="blazon-sub-0" src="' . $base . '" >';
        if( !($overlay1 === null) ) {
            $url = $this->getBlazonData($overlay1);
            if ($url !== '/media/file/wappen/'){ //string if job not saved
                $return .= '<img class="blazon-sub-1" src="' . $url . '" >';}
        }
        if( !($overlay2 === null) ) {
            if ( is_array($overlay2) ){
                $c = 1;
                foreach ($overlay2 as $blazon){
                    $url = $this->getBlazonData($blazon);
                    if ($url !== '/media/file/wappen/') { //string if not found
                        $return .= '<img class="blazon-sub-2 sub-' . $c . '" src="' . $url . '" >';
                    }
                }
            } else {
                $url = $this->getBlazonData($overlay2);
                if ($url !== '/media/file/wappen/') { //string if not found
                    $return .= '<img class="blazon-sub-2" src="' . $url . '" >';
                }
            }
        }
        $return .= '</div>';
        return $return;
    }

    public function getSingleBlazon($name, $size = 'small')
    {
        $base = ($size == 'small')? $this->getBlazonData($name[0]) : $this->getBlazonData($name[0], 'big');
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
    
    public function getBlazonHelperArgumentsByCharacter($character){
        if(!$this->parentBlazons)$this->parentBlazons = $this->service->getParentBlazons();
        $overlay1 = $overlay2 = '';
        if ($character['job_name'] !== null){
            $overlay1 = $character['job_name'];
        }
        if ( $character['supervisor_id'] !== "0" ) { //not set should be unused when in use
            if ( $character['supervisor_id'] !== "1" ) {    //first level Chars under fictive supervisor
                $overlay2 = (int)$this->parentBlazons[$character['supervisor_id']];
            }
        }
        $base = ($character['blazon_id'] == "0") ? 'standard' : (int)$character['blazon_id'];

        if ($character['id'] == "1") $overlay1 = 'king'; //special rule for the king
        if ($base == $overlay2) $overlay2 = null;

        return array($base, $overlay1, $overlay2);
    }
    public function resetParentBlazons()
    {
        $this->parentBlazons = false;
        $this->service->resetParentBlazons();
    }
}