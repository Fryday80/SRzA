<?php
namespace Application\Model;

use Application\Service\CacheService;
use Application\Utility\CircularBuffer;
use Auth\Service\AccessService;

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
    /** @var  $accessService AccessService */
    private $accessService;

    function __construct($sm)
    {
        $this->accessService = $sm->get('AccessService');
        /**** DATA SETS ****/
        $this->pageHitsSet    = new PageHitsSet($this->accessService);
        $this->activeUsersSet = new ActiveUsersSet($this->accessService);
        $this->actionsLogSet   = new actionsLogSet($this->accessService, $sm);
        $this->systemLogSet   = new SystemLogSet($this->accessService);
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
       $this->activeUsersSet->updateActive($sid, $ip, $this->getUserId(), $lastActionUrl, time(), $data);
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
    
    public function actionsLogToJSon($since = null)
    {
        return $this->actionsLogSet->toJSon($since);
    }

    public function actionsLogToArray($since = null)
    {
        return $this->actionsLogSet->toArray($since);
    }

    public function actionsLogGetJSonUpdate($last_id, $last_timestamp)
    {
        return $this->actionsLogSet->getJSonUpdate($last_id, $last_timestamp);
    }
    
    /**** SYSTEM LOG ****/
    /**
     * @param string $type
     * @param string $msg
     * @param int $userId
     * @param mixed $data
     */
    public function updateSystemLog($type, $msg, $data){
        $this->systemLogSet->updateSystemLog($type, $msg, $this->getUserId(), $data);
    }

    public function getSystemLogSet(){
        return $this->systemLogSet;
    }

    public function getSystemLog ($since = null){
        return $this->systemLogSet->getSystemLog ($since = null);
    }

    public function getSystemLogByType ($type, $since = null){
        return $this->systemLogSet->getSystemLogByType ($type, $since = null);
    }

    public function getSystemLogByUser ($userId, $since = null){
        return $this->systemLogSet->getSystemLogByUser ($userId, $since = null);
    }


    /**** PRIVATE METHODS ****/
    private function getUserId(){
        return ($this->accessService->getUserID() == "-1")? 0 : (int)$this->accessService->getUserID();
    }
}