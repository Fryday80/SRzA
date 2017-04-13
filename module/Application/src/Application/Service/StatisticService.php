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
        //Dummy data until all works fine
        $data = array (
            'ip' => '8.8.8.8',
            'sid' => '24-7-dev',
            'user_id' => 42,
            'last_action_time' => time(),
            'last_action_url' => '/dev',
            'action_data' => '$_SERVER',
        );
        $a = $this->sm->get('AccessService');

        $data['ip'] = $e->getApplication()->getRequest()->getServer('REMOTE_ADDR');
        $data['sid'] = $a->session->getManager()->getId();
        $actionData = $e->getApplication()->getRequest()->getServer()->toArray();
        //@todo erase unused data from $actionData
        $actionData = array ('bli' => 'bla', 'blubber' => 'blubb');
        $data['action_data'] = $actionData;

        $this->activeUsers->updateActive($data, $this->keepUserActive);
//        $this->activeUsers->getActiveUsers();  // for testing
        //@todo update pageHits DB
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