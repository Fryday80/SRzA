<?php
namespace Application\Controller;

use Application\Service\CacheService;
use Application\Service\StatisticService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Album\Model\Album;

class SystemController extends AbstractActionController
{
    public function dashboardAction()
    {
        /** @var  $statsService StatisticService */
        $statsService = $this->getServiceLocator()->get('StatisticService');
        $liveClicks = $statsService->getLastActions();
        $activeUsers = $statsService->getActiveUsers();
        return new ViewModel(array(
            'liveClicks' => $liveClicks,
            'activeUsers' => $activeUsers,
        ));
    }

    public function settingsAction()
    {

        return new ViewModel(array(
            //'table' => $albumsTable
        ));
    }
    public function jsonAction() {
        $a = json_decode($this->getRequest()->getContent());
//        $a->method;


        $result = new JsonModel(array(
            'some_parameter' => 'some value',
            'success'=>$this->getRequest()->getContent(),
        ));

        return $result;
    }
}
