<?php
namespace Application\Model;

use Application\Utility\CircularBuffer;

class Stats {
    /** @var CircularBuffer $actionLog */
    public $actionLog;
    /** @var SystemLog[] $systemLog */
    public $systemLog;
    /** @var PageHit[] $pageHits */
    public $pageHits;
    /** @var ActiveUser[] $activeUsers */
    public $activeUsers;
    /** @var  int[] $globalCounters */
    public $globalCounters;
    public $globalHitsSum = 0;
    public $globalErrorHitsSum = 0;
    public $realUserCount = 0;
    public $leaseTime = 30 * 60 * 1000000;
    private $key;

    function __construct() {
        $this->actionLog = new CircularBuffer(100);
        $this->systemLog = array();
        $this->pageHits = array();
        $this->activeUsers = array();
        $this->globalCounters = array_pad([], HitType::TYPES_COUNT, 0);
    }

    /**
     * @param ActiveUser $user
     */
    public function updateActiveUser( ActiveUser $user) {
        if (!isset($this->activeUsers[$user->sid])) {
            $this->activeUsers[$user->sid] = $user;
        } else {
            $this->activeUsers[$user->sid]->url  = $user->url;
            $this->activeUsers[$user->sid]->time = $user->time;
            $this->activeUsers[$user->sid]->data = $user->data;
            if ( ($user->userId !== 0) && ($user->userId !== $this->activeUsers[$user->sid]->userId) ) {
                $this->activeUsers[$user->sid]->userId = $user->userId;
                $this->activeUsers[$user->sid]->userName = $user->userName;
            }
        }

        //remove entries they are to old
        $newActiveUser = [];
        foreach($this->activeUsers as $key => $activeUser) {
            if ($activeUser->time > microtime(true) - $this->leaseTime) {
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

        $this->pageHits[$url]->lastTime = (int)microtime(true)*1000;
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
     * @param int $type
     * @return mixed
     */
    public function getPageHits($type = CounterType::ALL) {
        if ($type === CounterType::ALL) {
            return $this->globalHitsSum;
        }
        if ($type === CounterType::ERROR) {
            return $this->globalErrorHitsSum;
        }
        return $this->globalCounters[$type];
    }
    public function getActiveUsers($since = 0){
        if ($since == 0) return $this->sortByKey($this->activeUsers, 'time');
        return $this->getSinceOf($this->activeUsers, $since);
    }
    
    public function getActionLog($since = 0){
        $data = array_reverse($this->actionLog->toArray());
        if ($since !== 0) return $this->getSinceOf( $data, $since);
        return $data;
    }
    public function getMostVisitedPages($top = 1)
    {
        $result = array_reverse( $this->sortByKey($this->pageHits, 'hitsSum') );
        return array_slice($result, 0, $top);
    }
    
    private function getSinceOf($data, $since = 0){
        if( !isset( $data ) ) return null;
        $result = $this->filterByKey($data, 'time', $since, FilterTypes::BIGGER);
        return $this->sortByKey($result, 'time');
    }

    private function filterByKey( $data, $key, $value, $type = FilterTypes::EQUAL) {
        if( !isset( $data ) ) return null;
        $result = array();
        if ($type == FilterTypes::EQUAL) {
            foreach ($data as $item) {
                if ($item->$key == $value)
                    array_push($result, $item);
            }
        }
        if ($type == FilterTypes::BIGGER){
            foreach ($data as $item) {
                if ($item->$key > $value)
                    array_push($result, $item);
            }
        }
        if ($type == FilterTypes::SMALLER){
            foreach ($data as $item) {
                if ($item->$key < $value)
                    array_push($result, $item);
            }
        }
        if( empty($result) )return null;
        return $result;
    }

    function sort($a, $b) {
        $k = $this->key;
        $av = $a->$k;
        $bv = $b->$k;
        //@todo hate php
        if($av === $bv) {
            return  ($a->time < $b->time) ? -1 : 1;
        }

        return ($av < $bv)? -1: 1;
    }
    private function sortByKey($data, $key, $order = OrderTypes::DESCENDING){
        if( !isset( $data ) ) return null;
        $this->key = $key;
        usort($data, array($this, 'sort'));
        return $data;
    }


}

abstract class ActionType {
    const PAGE_CALL = 0;
    const ERROR = 1;

    static function translator($type){
        if ( $type == ActionType::PAGE_CALL )
            return 'Page Call';
        if ( $type == ActionType::ERROR )
            return 'Error';
    }
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
abstract class OrderTypes {
    const ASCENDING = 0;
    const DESCENDING = 1;
}
abstract class FilterTypes {
    const EQUAL = 0;
    const BIGGER = 1;
    const SMALLER = 2;
}