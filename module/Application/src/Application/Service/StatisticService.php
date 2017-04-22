<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 13.04.2017
 * Time: 02:13
 */

namespace Application\Service;

use Application\Model\Action;
use Application\Model\ActionType;
use Application\Model\ActiveUser;
use Application\Model\CounterType;
use Application\Model\HitType;
use Application\Model\Stats;
use Application\Model\SystemLog;
use Auth\Service\AccessService;
use Zarganwar\PerformancePanel\Register;
use Zend\Http\Header\SetCookie;
use Zend\Mvc\MvcEvent;

const STORAGE_PATH = '/storage/stats.log'; //relative to root, start with /
const WHITE_LIST = array('/');
/** "true" logs speed in Tracy "false" don't */
const SPEED_CHECK = true;

class StatisticService
{
    private $storagePath;
    /** @var Stats $storage */
    private $stats;
    private $sm;
    /** @var  AccessService */
    private $accessService;

    function __construct($sm)
    {
        if (SPEED_CHECK) Register::add('StatService start');
        $this->sm = $sm;
        $this->accessService = $sm->get('AccessService');
        $this->storagePath = getcwd().STORAGE_PATH;
        $this->stats = (file_exists($this->storagePath)) ? $this->loadFile() : new Stats();
        if (SPEED_CHECK) Register::add('StatService constructed');
    }

    public function onDispatch(MvcEvent $e)
    {
        $data = $this->gatherData($e);

        if($data['request']->isXmlHttpRequest() && !in_array($data['url'], WHITE_LIST)) return;

        $this->stats->logAction(new Action($data['time'], $data['url'], $data['userId'], $data['userName'], ActionType::PAGE_CALL , 'Call', $data['url']));
        $this->stats->logPageHit(($this->accessService->hasIdentity())? HitType::MEMBER : HitType::GUEST, $data['url']);
        $this->stats->updateActiveUser( new ActiveUser($data['userId'], $data['userName'], $data['time'], $data['sid'], $data['ip'], $data['url']) );

        $this->checkCookie($e);
    }

    public function onError(MvcEvent $e) {
        $data = $this->gatherData($e);

        $this->stats->logAction(new Action($data['time'], $data['url'], $data['userId'], $data['userName'], ActionType::ERROR , 'Call', $data['url']));
        $this->stats->logPageHit($data['hitType'], $data['url']);
        $this->stats->logSystem( new SystemLog($data['time'], $data['logType'], 'message', $data['url'], $data['userId'], $data['userName'], $data['serverPHPData'] ));
    }
    public function onFinish(MvcEvent $e)
    {
        bdump($this->stats);
        $this->saveFile($this->stats);
    }

    public function getPageHits ($count = CounterType::ALL){
        return $this->stats->getPageHits($count);
    }
    public function getActiveUsers($since = 0){
        return $this->stats->getActiveUsers($since);
    }

    /**
     * @param int $since timestamp microtime()*1000
     * @return array|mixed
     */
    public function getActionLog($since = 0){
        return $this->stats->getActionLog($since);
    }
    public function getMostVisitedPages($top = 1){
        return $this->stats->getMostVisitedPages($top);
    }
    
    
    private function checkCookie(MvcEvent $e) {
        if (!$e->getRequest()->getCookie() || !$e->getRequest()->getCookie()->offsetExists('srzaiknowyou')) {
            $this->stats->logNewUser();
            $cookie = new SetCookie('srzaiknowyou', time(), time() + 9999999);
            $e->getResponse()->getHeaders()->addHeader($cookie);
            $this->getPageHits(0);
        }
    }
    private function saveFile($content) {
        $content = serialize($content);
        file_put_contents($this->storagePath, $content);
        return true;
    }
    private function loadFile() {
        if (SPEED_CHECK) Register::add('load and unserialize');
        $content = file_get_contents($this->storagePath);
        $content = unserialize($content);
        if (SPEED_CHECK) Register::add('load and unserialize end');
        return $content;
    }

    private function gatherData($e)
    {
        if (SPEED_CHECK) Register::add('StatService ->gatherData start');
        $data['time'] = (int)microtime(true) * 1000;

        $data['sid']= $this->accessService->session->getManager()->getId();
        $data['userId']= $this->accessService->getUserID();
        $data['userName']= $this->accessService->getUserName();
        $data['userName']= ($data['userName']== "") ? "Guest" : $data['userName'];
        $data['hitType'] = ($this->accessService->hasIdentity())? HitType::MEMBER : HitType::GUEST;
        $data['logType'] = ($data['hitType'] == HitType::MEMBER) ? LogTypes::ERROR_MEMBER : LogTypes::ERROR_GUEST;

        $data['request']= $e->getApplication()->getRequest();
        $data['serverPHPData']= $data['request']->getServer()->toArray();
        $data['url']= $data['serverPHPData']['REQUEST_URI'];
        $data['ip']= $data['serverPHPData']['REMOTE_ADDR'];
        if (isset ($data['serverPHPData']['HTTP_REFERER']) ) {
            // prepared if referring data is needed
//        $data['replace']= array( "http://", $data['serverPHPData']['HTTP_HOST'] );
//        $data['referrer']= (isset ($data['serverPHPData']['HTTP_REFERER']) ) ? $data['serverPHPData']['HTTP_REFERER'] : "direct call";
//        $data['relativeReferrerURL']= str_replace( $data['replace'],"", $data['referrer'], $counter );
//        $data['redirect']= (isset ($data['serverPHPData']['REDIRECT_STATUS'])) ? $data['serverPHPData']['REDIRECT_STATUS'] : "no redirect"; //set if redirected
//        $data['redirectedTo']= (isset ($data['serverPHPData']['REDIRECT_URL']) ) ? $data['serverPHPData']['REDIRECT_URL'] : "no redirect";
        }
        if (SPEED_CHECK) Register::add('StatService ->gatherData end');
        return $data;
    }

    private function ifYou($wantToSee, $oldServiceFunctions)
    {
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
}
abstract class LogTypes {
    const ERROR_GUEST = 0;
    const ERROR_MEMBER = 1;
}