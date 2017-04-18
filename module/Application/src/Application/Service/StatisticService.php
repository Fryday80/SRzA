<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 13.04.2017
 * Time: 02:13
 */

namespace Application\Service;

use Application\Model\ActionLogSet;
use Application\Model\ActiveUsersSet;
use Application\Model\StatisticDataCollection;
use Auth\Service\AccessService;
use Zend\Mvc\MvcEvent;



class StatisticService
{
    /** STORAGE */
    private $storagePath = '/storage';
    private $storageName = 'Logs';
    private $fileExtension = '.store';
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

        /**** STORAGE ****/
        $this->storagePath = realpath(getcwd().$this->storagePath).'/';
        $this->collection = ($this->loadFile($this->storageName)) ? $this->loadFile($this->storageName) : new StatisticDataCollection($sm);
    }

    /**** EVENTS ****/
    public function onDispatch(MvcEvent $e)
    {
        /** @var  $a AccessService*/
        $a = $this->sm->get('AccessService');
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
        $activeUserData['user_id'] = ($a->getUserID() == "-1")? 0 : (int)$a->getUserID();
        $activeUserData['data'] = array();
        $activeUserData['last_action_url'] = $serverPHPData['REQUEST_URI'];

        $activeUserData['data']['serverData'] = $serverPHPData;
        
        $this->actionLog('site call', 'onDispatch', 'regular call', $activeUserData);
        $this->updatePageHit( $serverPHPData['REQUEST_URI'], $now, $activeUserData['user_id']);
//
//        $this->activeUsersTable->updateActiveUsers( $activeUserData, $this->keepUserActiveFor );
//
//        array_push($activeUserData['data'], array($redirect, $redirectedTo, $activeUserData['user_id']));
//        $this->logAction('Site call', 'regular log', 'call ' . $activeUserData['last_action_url'], $activeUserData);
//
//        $this->saveFile($this->storageName, $this->collection);
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
    
    
    /**** DATA COLLECTION SAVE & RESTORE ****/

    private function saveFile($name, $content, $serialize = true) {
        if ($serialize)
            $content = serialize($content);


        $folders = explode('/', $name);
        $file = array_pop($folders);
        $lastPath = $this->storagePath;
        if(substr($lastPath, -1) == '/') {
            $lastPath = substr($lastPath, 0, -1);
        }
        foreach ($folders as $key => $value) {
            $path = $lastPath.'/'.$value;
            if (!is_dir($path))
                mkdir($path);
            $lastPath = $path;
        }

        $a = file_put_contents($this->realPath($name), $content);

        return true;
    }
    private function realPath($name) {
        if (!$name || $name == '')
            return false;

        $path = $this->storagePath.$name.$this->fileExtension;
        return $path;
    }

    private function exists($name) {
        return file_exists($this->realPath($name));
    }
    private function loadFile($name, $serialize = true) {
        if (!$this->exists($name))
            return false;

        $content = file_get_contents($this->realPath($name));
        if ($serialize)
            $content = unserialize($content);

        return $content;
    }
}