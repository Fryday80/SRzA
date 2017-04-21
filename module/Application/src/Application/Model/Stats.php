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
     * @param $userName
     * @param $userId
     * @param $url
     * @param $ip
     * @param $sid
     * @param null $data
     */
    public function updateActiveUser($userName, $userId, $url, $ip, $sid, $data = null) {
        if (!isset($this->activeUsers[$sid])) {
            $this->activeUsers[$sid] = array(
                'url' => $url,
                'userId' => $userId,
                'userName' => $userName,
                'lastActionTime' => microtime(true),
                'sid' => $sid,
                'ip' => $ip,
                'data' => $data
            );
        } else {
            $this->activeUsers[$sid]['url'] = $url;
            $this->activeUsers[$sid]['lastActionTime'] = microtime();
            $this->activeUsers[$sid]['data'] = $data;
        }

        //remove entries they are to old
        $newActiveUser = [];
        foreach($this->activeUsers as $key => $activeUser) {
            if ($activeUser['lastActionTime'] > microtime() - 10000 * 300) {
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
     * @param $type
     * @param $msg
     * @param $userId
     */
    public function logSystem($type, $msg, $userId) {
        array_push($this->systemLog, array(
            'type' => $type,
            'message' => $msg,
            'userId' => $userId,
            'time' => microtime()
        ));
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