<?php
namespace Application\Model;


class StatisticDataCollection
{
    /** @var $pageHits pageHitsSet */
    public $pageHitsSet;
    /** @var  $activeUsers ActiveUsersSet */
    public $activeUsersSet;
    /** @var  $actionsLogSet actionsLogSet */
    public $actionsLogSet;
    /** @var $systemLogSet SystemLogSet  */
    public $systemLogSet;
    
    private $userId;
    private $userName;
    
    function __construct()
    {
        /**** DATA SETS ****/
        $this->pageHitsSet    = new PageHitsSet();
        $this->activeUsersSet = new ActiveUsersSet();
        $this->actionsLogSet  = new actionsLogSet();
        $this->systemLogSet   = new SystemLogSet();
    }

    /**** SET ****/
    /**** ACTIVE USERS ****/
    public function updateActiveUsers($sid, $ip, $lastActionUrl, $data){
        $this->activeUsersSet->updateActiveUsers($sid, $ip, $lastActionUrl, $data);
    }
    public function getActiveGuests(){
        return $this->activeUsersSet->getActiveGuests();
    }
    public function getGuestCount(){
        return $this->activeUsersSet->getGuestCount();
    }
    /**** ACTIONS LOG ****/
    public function updateActionsLog($type, $title, $msg, $data = null){
        $this->actionsLogSet->updateActionsLog($type, $title, $msg, $data);
    }
    /**** PAGE HITS ****/
    public function updatePageHit($url, $data = null){
        $this->pageHitsSet->updatePageHit($url, $data);
    }
    /**** SYS LOG ****/
    public function updateSystemLog($type, $msg, $data){
        $this->systemLogSet->updateSystemLog($type, $msg, $data);
    }
    /**** GET ****/
    /**** ACTIVE USERS ****/
    public function getActiveUsers(){
        return $this->activeUsersSet->toArray();
    }
    /**** ACTIONS LOG ****/
    public function getActionsLog($since = null){
        return $this->actionsLogSet->toArray($since);
    }
    public function getActionsLogByIDAndTime($last_id, $last_timestamp){
        return $this->actionsLogSet->getByIDAndTime($last_id, $last_timestamp);
    }
    /**** PAGE HITS ****/
    public function getPageHits($since = null){
        return $this->pageHitsSet->toArray($since);
    }

    public function getByUrl($url){
        return $this->pageHitsSet->getByUrl($url);
    }

    public function getHitsByUrl($url){
        return $this->pageHitsSet->getHitsByUrl($url);
    }

    public function getAllHits(){
        return $this->pageHitsSet->getAllHits();
    }
    public function getMostVisitedPages($top = 1){
        return $this->pageHitsSet->getMostVisitedPages($top);
    }
    /**** SYS LOG ****/
    public function getSysLog($since = null){
        return $this->systemLogSet->toArray($since);
    }
    public function getSystemLogByType ($type, $since = null){
        return $this->systemLogSet->getSystemLogByType ($type, $since);
    }
    public function getSystemLogByUser ($userId, $since = null){
        return $this->systemLogSet->getSystemLogByUser ($userId, $since);
    }
    public function getNumberOfLogs(){
        return $this->systemLogSet->getNumberOfLogs();
    }
    /**** ACTIVE USER SET ****/
    public function setUserId($id){
        if ($this->userId !== $id) {
            $this->userId = $id;
            $this->pageHitsSet->setUserId($id);
            $this->activeUsersSet->setUserId($id);
            $this->actionsLogSet->setUserId($id);
            $this->systemLogSet->setUserId($id);
        }
    }
    public function setUserName($name){
        if($this->userName !== $name) {
            $this->userName = $name;
            $this->pageHitsSet->setUserName($name);
            $this->activeUsersSet->setUserName($name);
            $this->actionsLogSet->setUserName($name);
            $this->systemLogSet->setUserName($name);
        }
    }
}