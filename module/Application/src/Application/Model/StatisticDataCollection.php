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
        $this->actionsLogSet   = new actionsLogSet();
        $this->systemLogSet   = new SystemLogSet();
    }

    /**** PAGE HITS COUNTER ****/

    /** Add page hit
     * @param string $url
     * @param int $time UNIX timestamp
     * @param int $lastUserId
     * @param null $data
     */
    public function updatePageHit($url, $data = null)    {
        $this->pageHitsSet->updatePageHit($url, $data);
    }

    public function getPageHitSet()    {
        return $this->pageHitsSet;
    }

    /**
     * @param int $since UNIX timestamp
     * @return array array of Page objects
     */
    public function getAllPagesData($since = 0){
        return $this->pageHitsSet->getAllPagesData($since);
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

    /**** ACTIVE USERS ****/
    /**
     * @param $sid
     * @param $ip
     * @param $userId
     * @param $lastActionUrl
     * @param $time
     * @param null $data
     */
    public function updateActive($sid, $ip, $lastActionUrl, $data = null){
       $this->activeUsersSet->updateActive($sid, $ip, $this->userId(), $lastActionUrl, time(), $data);
    }
    
    public function getActiveUsersSet(){
        return $this->activeUsersSet;
    }

    public function getActiveGuests(){
        return $this->activeUsersSet->getActiveGuests();
    }

    public function getGuestCount(){
        return $this->activeUsersSet->getGuestCount();
    }

    public function getActiveUsers()
    {
        return $this->activeUsersSet->getActiveUsers();
    }

    /**** ACTION LOG - CIRCULAR BUFFER ****/
    /**
     * @param $type string
     * @param $title string
     * @param $msg string
     * @param $data mixed (serializable)
     */
    public function updateActionsLog($type, $title, $msg, $data) {
        $this->actionsLogSet->updateActionsLog($type, $title, $msg, $data);
    }

    public function getActionsLogSet(){
        return $this->actionsLogSet;
    }

    public function actionsLogToArray($since = null)
    {
        return $this->actionsLogSet->toArray($since);
    }

    public function actionsLogGetByIDAndTime($last_id, $last_timestamp)
    {
        return $this->actionsLogSet->getByIDAndTime($last_id, $last_timestamp);
    }
    
    /**** SYSTEM LOG ****/
    /**
     * @param string $type
     * @param string $msg
     * @param int $userId
     * @param mixed $data
     */
    public function updateSystemLog($type, $msg, $data){
        $this->systemLogSet->updateSystemLog($type, $msg, $this->userId(), $data);
    }

    public function getSystemLogSet(){
        return $this->systemLogSet;
    }

    public function getSystemLog ($since = null){
        return $this->systemLogSet->getSystemLog ($since);
    }

    public function getSystemLogByType ($type, $since = null){
        return $this->systemLogSet->getSystemLogByType ($type, $since);
    }

    public function getSystemLogByUser ($userId, $since = null){
        return $this->systemLogSet->getSystemLogByUser ($userId, $since);
    }

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