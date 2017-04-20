<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 13.04.2017
 * Time: 02:13
 */

namespace Application\Service;

use Application\Model\Action;
use Application\Model\Stats;
use Auth\Service\AccessService;
use Zend\Mvc\MvcEvent;

const STORAGE_PATH = '/storage/stats.log'; //relative to root, start with /
const WHITE_LIST = array('/');

class ActionType {
    const PAGE_CALL = 0;
}
class StatisticService
{
    private $storagePath;
    /** @var Stats $storage */
    private $stats;
    private $sm;
    function __construct($sm)
    {
        $this->sm = $sm;
        $this->storagePath = getcwd().STORAGE_PATH;
        $this->stats = (file_exists($this->storagePath)) ? $this->loadFile() : new Stats();
    }

    /**** EVENT HANDLER ****/
    public function onDispatch(MvcEvent $e)
    {
        /** @var  $a AccessService*/
        $a = $this->sm->get('AccessService');
        $request = $e->getApplication()->getRequest();
        $serverPHPData = $request->getServer()->toArray();
        $now = time();
        $userId = $a->getUserID();
        $userId = ($userId == "-1")? 0 : (int)$userId;
        $userName = $a->getUserName();
        $userName = ($userName == "") ? "Guest" : $userName;
        $sid = $a->session->getManager()->getId();
        $url = $activeUserData['last_action_url'] = ($serverPHPData['REQUEST_URI'] == '/') ? '/Home' : $serverPHPData['REQUEST_URI'];
        if($request->isXmlHttpRequest() && in_array($url, WHITE_LIST)) return;
//        if( $this->ajaxFilter( $e->getApplication()->getRequest()->isXmlHttpRequest(), $url ) ) return ; //@todo
        $replace = array( "http://", $serverPHPData['HTTP_HOST'] );
        $referrer = (isset ($serverPHPData['HTTP_REFERER']) ) ? $serverPHPData['HTTP_REFERER'] : "direct call";
        $relativeReferrerURL = str_replace( $replace,"", $referrer, $counter );
        $redirect = (isset ($serverPHPData['REDIRECT_STATUS']))? $serverPHPData['REDIRECT_STATUS'] : "no redirect"; //set if redirected
        $redirectedTo = (isset ($serverPHPData['REDIRECT_URL']) ) ? $serverPHPData['REDIRECT_URL'] : "no redirect";
        
        // active users data
        $activeUserData['time'] = $now;
        $activeUserData['ip'] = $ip = $serverPHPData['REMOTE_ADDR'];
        $activeUserData['sid'] = $sid;
        $activeUserData['userId'] = $userId;
        $activeUserData['userName'] = $userName;
        $activeUserData['data'] = array();

        $activeUserData['data']['serverData'] = $serverPHPData;
        $this->stats->updateActiveUser($userName, $userId, $url, $ip, $sid, $activeUserData);
        $this->stats->logAction(new Action($url, $userId, ActionType::PAGE_CALL , 'Call', $url));
        $this->stats->logPageHit($url, $userId);

    }
    public function onError(MvcEvent $e) {
//        /** @var \Exception $exception */
//        $exception = $e->getResult()->exception;
//        $this->updateSystemLog("ROUTING", $exception->getMessage(), $e->getApplication()->getRequest()->getServer('REMOTE_ADDR'));
//        $this->saveFile($this->collection);
    }
    public function onFinish()
    {
//        $this->cache->setCache($this::ACTIONS_CACHE_NAME, $this->actionsLog);
        bdump($this->stats);
        $this->saveFile($this->stats);
    }

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

    public function getAllHits(){
        return $this->stats->getAllHits();
    }


//
//    /**** SET ****/
//    /**** ACTIVE USERS ****/
//    private function updateActiveUsers($url, $userId, $now, $sid, $ip, $userName, $data = null){
//        $this->collection->updateActiveUsers( array(
//            'url' => $url,
//            'expireDu' => $userId,
//            'time' => $now,
//            'sid' => $sid,
//            'ip' => $ip,
//            'userName' => $userName,
//            'data' => $data
//        ) );
//    }
//    /**** ACTIONS LOG ****/
//    private function updateActionsLog($url, $time, $userId, $userName, $type, $title, $msg, $data = null){
//        $this->collection->updateActionsLog( array(
//            'url' =>$url,
//            'time' =>$time,
//            'userId' =>$userId,
//            'userName' => $userName,
//            'type' =>$type,
//            'title' =>$title,
//            'msg' =>$msg,
//            'data' =>$data
//        ) );
//    }
//    /**** PAGE HITS ****/
//    private function updatePageHit($url, $time, $userId, $userName, $data = null){
//        $this->collection->updatePageHit( array(
//            'url' => $url,
//            'time' => $time,
//            'userId' => $userId,
//            'username' => $userName,
//            'data' => $data
//        ) );
//    }
//    /**** SYS LOG ****/
//    private function updateSystemLog( $url, $time, $type, $msg, $userId, $userName, $data = null ){
//        $this->collection->updateSystemLog( array(
//            'url' => $url,
//            'time' => $time,
//            'type' => $type,
//            'msg' => $msg,
//            'userId' => $userId,
//            'userName' => $userName,
//            'data' => $data
//        ) );
//    }
//
//
//    /**** GET ****/
//    /**** ACTIVE USERS ****/
//    public function getActiveUsers(){
//        return $this->collection->getActiveUsers();
//    }
//    public function getActiveGuests(){
//        return $this->collection->getActiveGuests();
//    }
//    public function getGuestCount(){
//        return $this->collection->getGuestCount();
//    }
//    /**** ACTIONS LOG ****/
//    public function getActionsLog($since = null){
//        return $this->collection->getActionsLog($since);
//    }
//    public function getActionsLogByIDAndTime($last_id, $last_timestamp){
//        return $this->collection->getActionsLogByIDAndTime($last_id, $last_timestamp);
//    }
//    /**** PAGE HITS ****/
//    public function getPageHits($since = null){
//        return $this->collection->getPageHits($since);
//    }
//    public function getByUrl($url){
//        return $this->collection->getByUrl($url);
//    }
//    public function getHitsByUrl($url){
//        return $this->collection->getHitsByUrl($url);
//    }
//    public function getAllHits(){
//        return $this->collection->getAllHits();
//    }
//    public function getMostVisitedPages($top = 1){
//        return $this->collection->getMostVisitedPages($top);
//    }
//    /**** SYS LOG ****/
//    public function getSysLog($since = null){
//        return $this->collection->getSysLog($since);
//    }
//    public function getSystemLogByType ($type, $since = null){
//        return $this->collection->getSystemLogByType ($type, $since);
//    }
//    public function getSystemLogByUser ($userId, $since = null){
//        return $this->collection->getSystemLogByUser ($userId, $since);
//    }
//
//    public function getNumberOfLogs(){
//        return $this->collection->getNumberOfLogs();
//    }
//
//    /**** DATA COLLECTION SAVE & RESTORE ****/

//
//    private function ajaxFilter($ajax, $url)
//    {
//        if ($ajax) {
//            // @todo whitelist
//            $whitelist = array('/');
//            if (in_array($url, $whitelist)) return true;
//        }
//        return false;
//    }
}