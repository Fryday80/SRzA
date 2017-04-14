<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 13.04.2017
 * Time: 02:13
 */

namespace Application\Service;

use Application\Model\ActiveUsers;
use Application\Model\PageHits;
use Application\Model\SystemLog;
use Auth\Service\AccessService;
use Zend\Mvc\MvcEvent;
use Application\Utility\CircularBuffer;

class Action {
    public $actionType; //string wie  loadPage, SystemLog, PageError ....
    public $time;
    public $msg; // bei loadPage die url, bei SystemLog die log msg ....
    public $userID;
    //...
}

class StatisticService
{
    const ACTIONS_CACHE_NAME = 'stats/actions';
    /** @var StatisticService  */
    private static $instance;
    private $sm;
    /** @var $activeUsers ActiveUsers */
    private $activeUsers ;
    /** @var $pageHits PageHits */
    private $pageHits;
    /** @var $systemLog SystemLog */
    private $systemLog;
    /** @var  $cache CacheService */
    private $cache;
    /** @var $actionsLog CircularBuffer */
    private $actionsLog;
    // Options
    private $keepUserActive = 30*60;

    function __construct($sm) {
        self::$instance = $this;
        $this->sm = $sm;
        $this->activeUsers = $this->sm->get('Application\Model\ActiveUsers');
        $this->pageHits = $this->sm->get('Application\Model\PageHits');
        $this->systemLog = $this->sm->get('Application\Model\SystemLog');
        $this->cache = $this->sm->get('CacheService');
        if (!$this->cache->hasCache($this::ACTIONS_CACHE_NAME)) {
            $this->actionsLog = new CircularBuffer(100);
            $this->cache->setCache($this::ACTIONS_CACHE_NAME, $this->actionsLog);
        } else {
            $this->actionsLog = $this->cache->getCache($this::ACTIONS_CACHE_NAME);
        }
    }

    public function onDispatch(MvcEvent $e) {
        /** @var  $a AccessService*/
        $a = $this->sm->get('AccessService');
        $serverPHPData = $e->getApplication()->getRequest()->getServer()->toArray();
        $now = time();
        $replace = array( "http://", $serverPHPData['HTTP_HOST'] );
        $referrer = (isset ($serverPHPData['HTTP_REFERER']) ) ? $serverPHPData['HTTP_REFERER'] : "direct call";
        $relativeReferrerURL = str_replace( $replace,"", $referrer, $counter );
        $redirect = (isset ($serverPHPData['REDIRECT_STATUS']))? $serverPHPData['REDIRECT_STATUS'] : "no redirect"; //set if redirected
        $redirectedTo = (isset ($serverPHPData['REDIRECT_URL']) ) ? $serverPHPData['REDIRECT_URL'] : "no redirect";

        // active users data
        $activeUserData['last_action_time'] = $now;
        $activeUserData['ip'] = $e->getApplication()->getRequest()->getServer('REMOTE_ADDR');
        $activeUserData['sid'] = $a->session->getManager()->getId();
        $activeUserData['user_id'] = ($a->getUserID() == "-1")? 0 : (int)$a->getUserID();
        $activeUserData['action_data'] = array();
        $activeUserData['last_action_url'] = ($counter == 2)? $relativeReferrerURL : $referrer;
        //@todo erase unused data from $serverPHPData if wanted
        array_push($activeUserData['action_data'], $serverPHPData);

        //@todo update pageHits DB
        $this->pageHits->countHit( $serverPHPData['REQUEST_URI'], $now );
        $this->activeUsers->updateActive($activeUserData, $this->keepUserActive);
    }
    public function onFinish(){
        $this->cache->setCache($this::ACTIONS_CACHE_NAME, $this->actionsLog);
    }
    public function getActiveUsers() {
        return $this->activeUsers->getActiveUsers();
    }

    public function getLastActions($since = null) {
        //@todo load actions from actionLog
        //wenn since == null load all
        //ansonsten nur alle die neuer sind
    }

    /**
     * @param $type string
     * @param $title string
     * @param $msg string
     * @param $data mixed (serializable)
     */
    public static function log($type, $title, $msg, $data) {
        //achtung static hier giebts kein this
        if (!self::$instance)
            return;

        $thiss = self::$instance;
        //@todo serialize $data
        //@todo write to DB
    }
    private function logAction() {
        $action = new Action();
        //@todo fill action
        $this->actionsLog->push($action);
    }
}