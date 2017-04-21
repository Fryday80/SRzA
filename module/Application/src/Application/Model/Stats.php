<?php
namespace Application\Model;

use Application\Utility\CircularBuffer;

class Stats {
    public $count = 0;
    /** @var CircularBuffer $actionLog */
    public $actionLog;
    public $systemLog;
    /** @var PageHit[] $pageHits */
    public $pageHits;
    public $activeUsers;
    public $globalCounters;
    public $globalHitsSum = 0;
    public $globalErrorHitsSum = 0;
    public $realUserCount = 0;

    function __construct() {
        $this->actionLog = new CircularBuffer(100);
        $this->systemLog = array();
        $this->pageHits = array();
        $this->globalCounters = array_pad([], HitType::TYPES_COUNT, 0);
    }

    /**
     * @param ActiveUser $user
     */
    public function updateActiveUser( ActiveUser $user) {
        if (!isset($this->activeUsers[$user->sid])) {
            $user->time = microtime(true);
        } else {
            $this->activeUsers[$user->sid]->url  = $user->url;
            $this->activeUsers[$user->sid]->time = microtime();
            $this->activeUsers[$user->sid]->data = $user->data;
            if ( ($user->userId !== 0) && ($user->userId !== $this->activeUsers[$user->sid]->userId) ) {
                $this->activeUsers[$user->sid]->userId = $user->userId;
                $this->activeUsers[$user->sid]->userName = $user->userName;
            }
        }

        //remove entries they are to old
        $newActiveUser = [];
        foreach($this->activeUsers as $key => $activeUser) {
            if ($activeUser->time > microtime() - 10000 * 300) {
                $newActiveUser[$key] = $activeUser;
            }
        }
        $this->activeUsers = $newActiveUser;
    }

    /**
     * @param Action $action
     */
    public function logAction(Action $action) {
        $this->actionLog->push($action);
    }

    /**
     * @param SystemLog $log
     */
    public function logSystem(SystemLog $log) {
        array_push($this->systemLog, $log);
    }

    /**
     * @param $hitType
     * @param $url
     */
    public function logPageHit($hitType, $url) {
        if (!isset($this->pageHits[$url])) {
            $this->pageHits[$url] = new PageHit($url);
        }

        $this->pageHits[$url]->lastTime = microtime(true);
        if ($hitType === HitType::GUEST || $hitType === HitType::MEMBER) {
            $this->pageHits[$url]->hitsSum++;
            $this->globalHitsSum++;
        }
        if ($hitType === HitType::ERROR_GUEST || $hitType === HitType::ERROR_MEMBER) {
            $this->pageHits[$url]->errorHitsSum++;
            $this->globalErrorHitsSum++;
        }
        $this->pageHits[$url]->counters[$hitType]++;
        $this->globalCounters[$hitType]++;
    }

    public function logNewUser() {
        $this->realUserCount++;
    }
    /**
     * @param $count of
     * @return array
     */
    public function getPageHits($count) {
        return array();
    }
    public function logActiveUser (ActiveUser $activeUserItem){
        
    }
}

abstract class ActionType {
    const PAGE_CALL = 0;
    const ERROR = 1;
}
abstract class HitType {
    const MEMBER = 0;
    const GUEST = 1;
    const ERROR_MEMBER = 2;
    const ERROR_GUEST = 3;
    const TYPES_COUNT = 4;//actually no type. keep it at bottom with the highest int
}
abstract class CounterType {
    const MEMBER = 0;
    const GUEST = 1;
    const ERROR_MEMBER = 2;
    const ERROR_GUEST = 3;
    const ALL = 4;
    const ERROR = 5;
}