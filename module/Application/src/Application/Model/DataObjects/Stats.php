<?php
namespace Application\Model\DataObjects;

use Application\Model\Abstracts\CounterType;
use Application\Model\Abstracts\FilterType;
use Application\Model\Abstracts\HitType;
use Application\Model\Abstracts\OrderType;
use Application\Utility\CircularBuffer;

class Stats {
    /** @var CircularBuffer $actionLog */
    public $actionLog;
    /** @var PageHit[] $pageHits */
    public $pageHits;
    /** @var ActiveUser[] $activeUsers */
    public $activeUsers;
    /** @var  int[] $globalCounters */
    public $globalCounters;
    public $globalHitsSum = 0;
    public $globalErrorHitsSum = 0;
    public $realUserCount = 0;
    public $leaseTime = 30 * 60;
    private $key;
    /** @var int userIds for guests */
    public $guestId;
    public $guestNumbersMax = 100000;
    public $guestNumbersMin =  90000;

    function __construct() {
        $this->actionLog = new CircularBuffer(100);
        $this->pageHits = array();
        $this->activeUsers = array();
        $this->globalCounters = array_pad([], HitType::TYPES_COUNT, 0);
        $this->guestId = $this-> guestNumbersMax;
    }

    /**
     * @param ActiveUser $user
     * @param $sid
     */
    public function updateActiveUser( ActiveUser $user, $sid) {
        if (!isset($this->activeUsers[$sid])) {
            $this->activeUsers[$sid] = $user;
        } else {
            $this->activeUsers[$sid]->url  = $user->url;
            $this->activeUsers[$sid]->time = $user->time;
            $this->activeUsers[$sid]->microtime = $user->microtime;
            $this->activeUsers[$sid]->data = $user->data;
            if ( ($user->userId !== 0) && ($user->userId !== $this->activeUsers[$sid]->userId) ) {
                $this->activeUsers[$sid]->userId = $user->userId;
                $this->activeUsers[$sid]->userName = $user->userName;
            }
        }

        //remove entries that are to old
        $newActiveUser = [];
        foreach($this->activeUsers as $key => $activeUser) {
            if ($activeUser->time > time() - $this->leaseTime) {
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
     * @param $hitType
     * @param string $url
     * @param float $mTime microtime(true)
     */
    public function logPageHit($hitType, $url, $mTime) {
        if (!isset($this->pageHits[$url])) {
            $this->pageHits[$url] = new PageHit($url, $mTime);
        }

        $this->pageHits[$url]->updateTime($mTime);
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

    /**
     * @param float $since optional to get data since microtime timestamp
     * @return array|null sorted result array new -> old
     */
    public function getActiveUsers($since = 0){
        if ($since == 0) return $this->sortByKey($this->activeUsers, 'microtime');
        return $this->getSinceOf($this->activeUsers, $since);
    }

    /** get a unique guestId
     * @param $sid
     * @return int
     */
    public function getActiveGuestId($sid){
        if (isset($this->activeUsers[$sid])) return $this->activeUsers[$sid]->userId;

        $gId = $this->guestId;
        $this->guestId--;
        //reset guestIds
        if($this->guestId == $this->guestNumbersMin ) $this->guestId = $this->guestNumbersMax;
        return $gId;
    }

    /**
     * @param float $since timestamp microtime()
     * @return array array of results
     */
    public function getActionLog($since = 0){
        $data = array_reverse($this->actionLog->toArray());
        if ($since !== 0) return $this->getSinceOf( $data, $since);
        return $data;
    }
    
    /**
     * @param int $top number of top entries
     * @return array result array
     */
    public function getMostVisitedPages($top = 1)
    {
        $result = array_reverse( $this->sortByKey($this->pageHits, 'hitsSum') );
        return array_slice($result, 0, $top);
    }

    /** shorthand for getting data<br> sorted new to old<br> from now to $since
     * @param array $data array of DataItems objects
     * @param float $since microtime timestamp
     * @return null|array result array | null on failure
     */
    public function getSinceOf($data, $since = 0){
        if( !isset( $data ) ) return null;
        $since = $since + 0.0001;
        $result = $this->filterByKey($data, 'microtime', $since, FilterType::BIGGER);
        return $this->sortByKey($result, 'microtime');
    }

    /**
     * @param array $data
     * @param string $key name of the search key
     * @param mixed $value value of search
     * @param int $type optional search mode<br> 0 = equal = standard,<br> 1 = bigger,<br> 2 = smaller
     * @return array|null result array | null on failure
     */
    public function filterByKey( $data, $key, $value, $type = FilterType::EQUAL) {
        if( !isset( $data ) ) return null;
        $result = array();
        if ($type == FilterType::EQUAL) {
            foreach ($data as $item) {
                if ($item->$key == $value)
                    array_push($result, $item);
            }
        }
        elseif ($type == FilterType::BIGGER){
            foreach ($data as $item) {
                if ($item->$key > $value) {
                    array_push($result, $item);
                }
            }
        }
        elseif ($type == FilterType::SMALLER){
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

        if($av === $bv) {
            return  ($a->microtime < $b->microtime) ? -1 : 1;
        }
        return ($av < $bv)? -1: 1;
    }
    public function sortByKey($data, $key, $order = OrderType::DESCENDING){
        if( !isset( $data ) ) return null;
        $this->key = $key;
        usort($data, array($this, 'sort'));
        return $data;
    }
}