<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 13.04.2017
 * Time: 02:13
 */

namespace Application\Service;

use Application\Model\ActionsLogSet;
use Application\Model\ActiveUsersSet;
use Application\Model\PageHitsSet;
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
    /** @var  $cache CacheService */
    private $cache;
    
    /** OPTIONS */
    private $keepUserActiveFor = 30*60;

    function __construct($sm)
    {
        $this->sm = $sm;

        $a = serialize(new StatisticDataCollection());
//        var_dump($a);die;
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
        $this->collection->setUserId ($userId);
        $this->collection->setUserName ($userName);
        $serverPHPData = $e->getApplication()->getRequest()->getServer()->toArray();
        $ajax = $e->getApplication()->getRequest()->isXmlHttpRequest();
        if($ajax) return ; //@todo check if its in blacklist
        $now = time();
        $replace = array( "http://", $serverPHPData['HTTP_HOST'] );
        $referrer = (isset ($serverPHPData['HTTP_REFERER']) ) ? $serverPHPData['HTTP_REFERER'] : "direct call";
        $relativeReferrerURL = str_replace( $replace,"", $referrer, $counter );
        $redirect = (isset ($serverPHPData['REDIRECT_STATUS']))? $serverPHPData['REDIRECT_STATUS'] : "no redirect"; //set if redirected
        $redirectedTo = (isset ($serverPHPData['REDIRECT_URL']) ) ? $serverPHPData['REDIRECT_URL'] : "no redirect";
        // active users data
        $activeUserData['time'] = $now;
        $activeUserData['ip'] = $e->getApplication()->getRequest()->getServer('REMOTE_ADDR');
        $activeUserData['sid'] = $a->session->getManager()->getId();
        $activeUserData['user_id'] = $userId;
        $activeUserData['data'] = array();
        $activeUserData['last_action_url'] = $serverPHPData['REQUEST_URI'];

        $activeUserData['data']['serverData'] = $serverPHPData;
        
        $this->actionLog('site call', 'onDispatch', 'regular call', $activeUserData);
        $this->updatePageHit( $serverPHPData['REQUEST_URI'], $now, $activeUserData['user_id']);
        $this->updateActiveUsers($activeUserData['sid'], $activeUserData['ip'], $activeUserData['last_action_url'], $activeUserData);

        $this->saveFile($this->collection);
    }
    public function onFinish()
    {
//        $this->cache->setCache($this::ACTIONS_CACHE_NAME, $this->actionsLog);
    }
    /**** METHODS ****/

    public function getDataCollection(){
        return $this->collection;
    }
    public function getPageHitSet(){
        return $this->collection->getPageHitSet();
    }
    public function getActiveUsersSet(){
        return $this->collection->getActiveUsersSet();
    }
    public function getActionsLogSet(){
        return $this->collection->getActionsLogSet();
    }
    public function getSystemLogSet(){
        return $this->collection->getSystemLogSet();
    }
    
    
    public function actionLog($type, $title, $msg, $data){
        $this->collection->updateActionsLog($type, $title, $msg, $data);
    }

    public function updatePageHit($url, $user_id)
    {
        $this->collection->updatePageHit($url, $user_id);
    }

    public function updateActiveUsers($sid, $ip, $lastActionUrl, $data){
        $this->collection->activeUsersSet->updateActive($sid, $ip, $lastActionUrl, $data);
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