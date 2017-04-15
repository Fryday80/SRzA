<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 13.04.2017
 * Time: 02:13
 */

namespace Application\Service;

use Application\DataObjects\ActionLogSet;
use Application\Model\ActiveUsersTable;
use Application\Model\PageHitsTable;
use Application\Model\SystemLogTable;
use Auth\Service\AccessService;
use Zend\Mvc\MvcEvent;
use Application\Utility\CircularBuffer;
use Application\DataObjects\Action;



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
            $this->actionsLog = new CircularBuffer(1);
            $this->cache->setCache($this::ACTIONS_CACHE_NAME, $this->actionsLog);
        } else {
            $this->actionsLog = $this->cache->getCache($this::ACTIONS_CACHE_NAME);
        }
    }

    public function onDispatch(MvcEvent $e)
    {
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
        $activeUserData['time'] = $now;
        $activeUserData['ip'] = $e->getApplication()->getRequest()->getServer('REMOTE_ADDR');
        $activeUserData['sid'] = $a->session->getManager()->getId();
        $activeUserData['user_id'] = ($a->getUserID() == "-1")? 0 : (int)$a->getUserID();
        $activeUserData['action_data'] = array();
        $activeUserData['last_action_url'] = ($counter == 2)? $relativeReferrerURL : $referrer;

        $activeUserData['action_data']['serverData'] = $serverPHPData;


        $this->pageHitsTable->countHit( $serverPHPData['REQUEST_URI'], $now );
        $this->activeUsersTable->updateActiveUsers( $activeUserData, $this->keepUserActiveFor );

        array_push($activeUserData['action_data'], array($redirect, $redirectedTo, $activeUserData['user_id']));
        $this->logAction('Site call', 'regular log', 'call ' . $activeUserData['last_action_url'], $activeUserData);
    }
    public function onFinish()
    {
        $this->cache->setCache($this::ACTIONS_CACHE_NAME, $this->actionsLog);
    }

    // ---------------------------------- getter ----------------------------
    // ------------- Active Users -----------------------------
    public function getActiveUsers()
    {
        return $this->activeUsersTable->getActiveUsers();
    }

    // ------------- Action Log -------------------------------
    public function getLastActions($since = null)
    {
//        //@todo $since
        $data = array_reverse( $this->actionsLog->toArray() );
        return new ActionLogSet($data);
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
        //achtung static hier giebts kein this
        if (!self::$instance)
            return;

        $thiss = self::$instance;
        //@todo serialize $data
        //@todo write to DB
    }
    
    // ----------------- Action Log -------------------------
    private function logAction($type, $title, $msg, $data) {
        $a = $this->sm->get('AccessService');
        /** @var  $action Action */
        $action = new Action();
        // fill action
        $action->actionType = $type;
        $action->title  = $title;
        $action->msg = $msg;
        $action->data = $data;
        $action->time = ($data['time']) ? $data['time'] : time();
        $action->user_id = ($a->getUserID() == "-1")? 0 : (int)$a->getUserID();

        $this->actionsLog->push($action);
    }
}