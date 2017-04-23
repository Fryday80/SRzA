<?php
namespace Application\Model;

use Application\Model\Abstracts\HitType;

class PageHit {
    public $url;
    public $time;
    public $counters;
    public $hitsSum = 0;
    public $errorHitsSum = 0;

    function __construct($url) {
        $this->url = $url;
        $this->time = microtime(true) * 1000;
        $this->counters = array_pad([], HitType::TYPES_COUNT, 0);
    }
    public function getCount($type = null) {
        if (!$type) return $this->hitsSum + $this->errorHitsSum;
    }
}