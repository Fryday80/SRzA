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
    /**
     * @var $activeUsers ActiveUsers
     */
    private $activeUsers ;
    /**
     * @var $pageHits PageHits
     */
    private $pageHits;
    /**
     * @var $systemLog SystemLog
     */
    private $systemLog;

    function __construct($sm)
    {
        $this->sm = $sm;
        $this->activeUsers = $this->sm->get('Application\Model\ActiveUsers');
        $this->pageHits = $this->sm->get('Application\Model\PageHits');
        $this->systemLog = $this->sm->get('Application\Model\SystemLog');
    }

    public function onDispatch(MvcEvent $e) {
        //@todo update activeUsers DB
        //@todo update pageHits DB
        //über des mvcEvent kommt man an fast alle infos glaub ich
        bdump($e->getApplication()->getRequest());
        $ip = $e->getApplication()->getRequest()->getServer('REMOTE_ADDR');

    }

}