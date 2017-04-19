<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 13.04.2017
 * Time: 02:13
 */

namespace Application\Service;

use Application\Model\StatisticDataCollection;
use Auth\Service\AccessService;
use Zend\Mvc\MvcEvent;

const STORAGE_PATH = '/storage/stats.log'; //relative to root, start with /

class StatisticService
{
    /** STORAGE */
    private $storagePath;
    /** @var  $storage StatisticDataCollection */
    private $collection;
    
    /** VARS */
    private $sm;

    function __construct($sm)
    {
        $this->sm = $sm;

//        $a = serialize(new StatisticDataCollection());
//        var_dump($a);
//        die;
        /**** STORAGE ****/
        $this->storagePath = getcwd().STORAGE_PATH;
        $this->collection = (file_exists($this->storagePath)) ? $this->loadFile() : new StatisticDataCollection($sm);

    }

    /**** EVENTS ****/
    public function onDispatch(MvcEvent $e)
    {
        /** @var  $a AccessService*/
        $a = $this->sm->get('AccessService');
        $userId = ($a->getUserID() == "-1")? 0 : (int)$a->getUserID();
        $userName = $a->getUserName();
        $userName = ($userName == "") ? "Guest" : $userName;
        $this->updateActiveUserData($userId, $userName);
        $serverPHPData = $e->getApplication()->getRequest()->getServer()->toArray();
        $ajax = $e->getApplication()->getRequest()->isXmlHttpRequest();
        if($ajax) return ; //@todo check if its in blacklist
        $now = time();
        $replace = array( "http://", $serverPHPData['HTTP_HOST'] );
        $referrer = (isset ($serverPHPData['HTTP_REFERER']) ) ? $serverPHPData['HTTP_REFERER'] : "direct call";
//        $relativeReferrerURL = str_replace( $replace,"", $referrer, $counter );
//        $redirect = (isset ($serverPHPData['REDIRECT_STATUS']))? $serverPHPData['REDIRECT_STATUS'] : "no redirect"; //set if redirected
//        $redirectedTo = (isset ($serverPHPData['REDIRECT_URL']) ) ? $serverPHPData['REDIRECT_URL'] : "no redirect";
        
        // active users data
        $activeUserData['time'] = $now;
        $activeUserData['ip'] = $e->getApplication()->getRequest()->getServer('REMOTE_ADDR');
        $activeUserData['sid'] = $a->session->getManager()->getId();
        $activeUserData['userId'] = $userId;
        $activeUserData['userName'] = $userName;
        $activeUserData['data'] = array();
        $lastUrl = $activeUserData['last_action_url'] = ($serverPHPData['REQUEST_URI'] == '/') ? '/Home' : $serverPHPData['REQUEST_URI'];

        $activeUserData['data']['serverData'] = $serverPHPData;
        
        $this->updateActionsLog('site call', $lastUrl, 'regular call', $activeUserData);
        $this->updatePageHit( $lastUrl);
        $this->updateActiveUsers($activeUserData['sid'], $activeUserData['ip'], $lastUrl, $activeUserData);
        // for testing:
        if (( 5 > $this->getNumberOfLogs() ))
            $this->updateSystemLog('DUMMY', 'dummy', 'dummy data');


        $this->saveFile($this->collection);
    }
    public function onFinish()
    {
//        $this->cache->setCache($this::ACTIONS_CACHE_NAME, $this->actionsLog);
    }
    /**** METHODS ****/
    /**** SET ****/
    /**** ACTIVE USERS ****/
    public function updateActiveUsers($sid, $ip, $lastActionUrl, $data){
        $this->collection->updateActiveUsers($sid, $ip, $lastActionUrl, $data);
    }
    public function getActiveGuests(){
        return $this->collection->getActiveGuests();
    }
    public function getGuestCount(){
        return $this->collection->getGuestCount();
    }
    /**** ACTIONS LOG ****/
    public function updateActionsLog($type, $title, $msg, $data = null){
        $this->collection->updateActionsLog($type, $title, $msg, $data);
    }
    /**** PAGE HITS ****/
    public function updatePageHit($url, $data = null){
        $this->collection->updatePageHit($url, $data);
    }
    /**** SYS LOG ****/
    public function updateSystemLog($type, $msg, $data){
        $this->collection->updateSystemLog($type, $msg, $data);
    }
    public function getNumberOfLogs(){
        return $this->collection->getNumberOfLogs();
    }
    /**** GET ****/
    /**** ACTIVE USERS ****/
    public function getActiveUsers(){
        return $this->collection->getActiveUsers();
    }
    /**** ACTIONS LOG ****/
    public function getActionsLog($since = null){
        return $this->collection->getActionsLog($since);
    }
    public function getActionsLogByIDAndTime($last_id, $last_timestamp){
        return $this->collection->getActionsLogByIDAndTime($last_id, $last_timestamp);
    }
    /**** PAGE HITS ****/
    public function getPageHits($since = null){
        return $this->collection->getPageHits($since);
    }
    public function getByUrl($url){
        return $this->collection->getByUrl($url);
    }
    public function getHitsByUrl($url){
        return $this->collection->getHitsByUrl($url);
    }
    public function getAllHits(){
        return $this->collection->getAllHits();
    }
    public function getMostVisitedPages($top = 1){
        return $this->collection->getMostVisitedPages($top);
    }
    /**** SYS LOG ****/
    public function getSysLog($since = null){
        return $this->collection->getSysLog($since);
    }
    public function getSystemLogByType ($type, $since = null){
        return $this->collection->getSystemLogByType ($type, $since);
    }
    public function getSystemLogByUser ($userId, $since = null){
        return $this->collection->getSystemLogByUser ($userId, $since);
    }

    /**** PRIVATE HELPER ****/
    private function updateActiveUserData($userId,$userName){
        $this->collection->setUserId ($userId);
        $this->collection->setUserName ($userName);
    }
    /**** DATA COLLECTION SAVE & RESTORE ****/

    private function saveFile($content) {
        $content = serialize($content);
        file_put_contents($this->storagePath, $content);
        return true;
    }
    private function loadFile() {
        $content = file_get_contents($this->storagePath);
        $content = unserialize($content);
        return $content;
    }
}