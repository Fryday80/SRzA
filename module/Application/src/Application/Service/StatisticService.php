<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 13.04.2017
 * Time: 02:13
 */

namespace Application\Service;

use Application\Model\DataObjects\ActionLogSet;
use Application\Model\ActiveUsersTable;
use Application\Model\DashboardTables\PageHitsTable;
use Application\Model\SystemLogTable;
use Auth\Service\AccessService;
use Zend\Mvc\MvcEvent;
use Application\Utility\CircularBuffer;
use Application\Model\DataObjects\Action;



class StatisticService
{
    const ACTIONS_CACHE_NAME = 'stats/actions';
    /** @var StatisticService  */
    private static $instance;
    private $sm;
    /** @var $activeUsers ActiveUsersTable */
    private $activeUsersTable ;
    /** @var $pageHits PageHitsTable */
    private $pageHitsTable;
    /** @var $systemLog SystemLogTable */
    private $systemLogTable;
    /** @var  $cache CacheService */
    private $cache;
    /** @var $actionsLog CircularBuffer */
    private $actionsLog;
    // Options
    private $keepUserActiveFor = 30*60;

    function __construct($sm)
    {
        self::$instance = $this;
        $this->sm = $sm;
        $this->activeUsersTable = $this->sm->get('Application\Model\ActiveUsers');
        $this->pageHitsTable = $this->sm->get('Application\Model\PageHits');
        $this->systemLogTable = $this->sm->get('Application\Model\SystemLog');
        $this->cache = $this->sm->get('CacheService');
        if (!$this->cache->hasCache($this::ACTIONS_CACHE_NAME)) {
            $this->actionsLog = new CircularBuffer(100);
            $this->cache->setCache($this::ACTIONS_CACHE_NAME, $this->actionsLog);
        } else {
            $this->actionsLog = $this->cache->getCache($this::ACTIONS_CACHE_NAME);
        }
//        $this->log();
    }

    public function onDispatch(MvcEvent $e)
    {
        /** @var  $a AccessService*/
        $a = $this->sm->get('AccessService');
        $serverPHPData = $e->getApplication()->getRequest()->getServer()->toArray();
        $ajax = $e->getApplication()->getRequest()->isXmlHttpRequest();
//        if(!$ajax) return ; //@todo check if its in blacklist
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


        $this->pageHitsTable->countHit( $serverPHPData['REQUEST_URI'], $now );
        $this->activeUsersTable->updateActiveUsers( $activeUserData, $this->keepUserActiveFor );

        array_push($activeUserData['data'], array($redirect, $redirectedTo, $activeUserData['user_id']));
        $this->logAction('Site call', 'regular log', 'call ' . $activeUserData['last_action_url'], $activeUserData);
    }
    public function onFinish()
    {
        $this->cache->setCache($this::ACTIONS_CACHE_NAME, $this->actionsLog);
    }


    public function getActiveUsers()
    {
        return $this->activeUsersTable->getActiveUsers();
    }

    /**
     * @return ActionLogSet
     */
    public function getLastActions()
    {
        bdump(($this->actionsLog->toArray()));
        return new ActionLogSet($this->actionsLog->toArray());
    }
    
    public function getSystemLog()
    {
        return $this->systemLogTable->getSystemLogs();
    }

    public function getActiveUserDuration()
    {
        return $this->keepUserActiveFor;
    }

    /**
     * @param $type string
     * @param $title string
     * @param $msg string
     * @param $data mixed (serializable)
     */
    public static function log($type, $title, $msg, $data) {
        // static => $this = self::$instance;
        if (!self::$instance)
            return;

        self::$instance->systemLogTable->updateSystemLog($type, $title, $msg, $data);
    }

    /**
     * @param $type string
     * @param $title string
     * @param $msg string
     * @param $data mixed (serializable)
     */
    private function logAction($type, $title, $msg, $data) {
        $a = $this->sm->get('AccessService');
        /** @var  $action Action */
        $action = new Action();
        // fill action
        $action->actionType = $type;
        $action->title  = $title;
        $action->msg = $msg;
        $action->data = $data;
        $action->time = time();
        $action->user_id = ($a->getUserID() == "-1")? 0 : (int)$a->getUserID();
        bdump('action push');
        bdump($action);
        $this->actionsLog->push($action);
    }
}