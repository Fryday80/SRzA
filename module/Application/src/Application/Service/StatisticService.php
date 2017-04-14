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


class StatisticService
{
    private $sm;
    /** @var $activeUsers ActiveUsers */
    private $activeUsers ;
    /** @var $pageHits PageHits */
    private $pageHits;
    /** @var $systemLog SystemLog */
    private $systemLog;

    // Options
    private $keepUserActive = 30*60;

    function __construct($sm)
    {
        $this->sm = $sm;
        $this->activeUsers = $this->sm->get('Application\Model\ActiveUsers');
        $this->pageHits = $this->sm->get('Application\Model\PageHits');
        $this->systemLog = $this->sm->get('Application\Model\SystemLog');
    }

    public function onRedirectNoPerm(){}
    public function onDispatch(MvcEvent $e) {
        /** @var  $a AccessService*/
        $a = $this->sm->get('AccessService');
        $serverPHPData = $e->getApplication()->getRequest()->getServer()->toArray();
        $now = time();
        $replace = array( "http://", $serverPHPData['HTTP_HOST'] );
        $referrer = $serverPHPData['HTTP_REFERER'];
        $relativeReferrerURL = str_replace( $replace,"", $referrer, $counter );
        $redirect = (isset ($serverPHPData['REDIRECT_STATUS']))? $serverPHPData['REDIRECT_STATUS'] : null; //set if redirected
        $redirectedTo = (isset ($serverPHPData['REDIRECT_URL']) ) ? $serverPHPData['REDIRECT_URL'] : null;

        // active users data
        $activeUserData['last_action_time'] = $now;
        $activeUserData['ip'] = $e->getApplication()->getRequest()->getServer('REMOTE_ADDR');
        $activeUserData['sid'] = $a->session->getManager()->getId();
        $activeUserData['user_id'] = ($a->getUserID() == "-1")? 0 : $a->getUserID();
        $activeUserData['action_data'] = array();
        $activeUserData['last_action_url'] = ($counter == 2)? $relativeReferrerURL : $serverPHPData['HTTP_REFERER'];
        //@todo erase unused data from $serverPHPData if wanted
        array_push($activeUserData['action_data'], $serverPHPData);

        //@todo update pageHits DB
//        $this->pageHits->countHit( $serverPHPData['REQUEST_URI'], $now );
        $this->activeUsers->updateActive($activeUserData, $this->keepUserActive);
//        bdump($serverPHPData);
    }

    public function getActiveUsers()
    {
        return $this->activeUsers->getActiveUsers();
    }

    /**
     * @param $type string
     * @param $title string
     * @param $msg string
     * @param $data mixed (serializable)
     */
    public static function log($type, $title, $msg, $data) {
        //@todo serialize $data
        //@todo write to DB
    }
}