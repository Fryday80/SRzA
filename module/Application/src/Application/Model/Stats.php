<?php
namespace Application\Model;

use Application\Utility\CircularBuffer;
class Stats {
    public $count = 0;
    public $actionLog;
    public $systemLog;
    public $pageHits;
    public $activeUsers;

    function __construct() {
        $this->actionLog = new CircularBuffer(100);
        $this->systemLog = array();
        $this->pageHits = array();
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
                'lastActionTime' => microtime(),
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
     * @param $url
     * @param $userId
     * @param null $data
     */
    public function logPageHit($url, $userId, $data = null) {
        if (!isset($this->pageHits[$url])) {
            $this->pageHits[$url] = array(
                'url' => $url,
                'lastTime' => microtime(),
                'count' => 1
            );
        } else {
            $this->pageHits[$url]['count']++;
            $this->pageHits[$url]['lastTime'] = microtime();
        }

    }

    /**
     * @param $count of
     * @return array
     */
    public function getPageHits($count) {
        return array();
    }
}