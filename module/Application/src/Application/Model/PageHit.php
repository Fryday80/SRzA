<?php
namespace Application\Model;

use Application\Model\Abstracts\HitType;

class PageHit {
    public $id;
    public $url;
    public $time;
    public $counters;
    public $hitsSum = 0;
    public $errorHitsSum = 0;

    function __construct($url, $mTime) {
        $this->url = $url;
        $this->id = $mTime * 10000;
        $this->time = (int)$mTime;
        $this->counters = array_pad([], HitType::TYPES_COUNT, 0);
    }
    public function getCount($type = null) {
        if (!$type) return $this->hitsSum + $this->errorHitsSum;
    }
}