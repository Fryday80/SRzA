<?php
namespace Application\Model\DataObjects;

use Application\Model\Abstracts\HitType;

class PageHit extends DataItem
{
    public $url;
    public $counters;
    public $hitsSum = 0;
    public $errorHitsSum = 0;

    function __construct($url, $mTime) {
        parent::__construct($mTime);
        $this->url = $url;
        $this->counters = array_pad([], HitType::TYPES_COUNT, 0);
    }
    public function getCount($type = null) {
        if (!$type) return $this->hitsSum + $this->errorHitsSum;
    }
}