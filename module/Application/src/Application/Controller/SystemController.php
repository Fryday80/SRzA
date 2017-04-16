<?php
namespace Application\Controller;


use Application\Model\DataObjects\DashboardData;
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
//        /** @var  $statsService StatisticService */
//        $statsService = $this->getServiceLocator()->get('StatisticService');
        $dashboardData = new DashboardData( $this->getServiceLocator() );
        $dashboardData->setActionLog();
        $dashboardData->setActiveUsers();
        $activeUsers = $dashboardData->getActiveUsers();
        $aUc = count( $activeUsers->toArray() );
        return new ViewModel(array(
            'dashboardData' => $dashboardData,
            'activeUserCount' => $aUc,
        ));
    }

    public function settingsAction()
    {

        return new ViewModel(array(
            //'table' => $albumsTable
        ));
    }
    public function jsonAction() {
        /** @var  $statsService StatisticService */
        $statsService = $this->getServiceLocator()->get('StatisticService');
        $request = json_decode($this->getRequest()->getContent());
        $result = ['error' => false];
        switch ($request->method) {
            case 'getLiveActions':
                //@todo check parameter since if exists (dann bei allen hier)
                $statsService->getLastActions($request->since);
                break;
        };

        //output
        return new JsonModel($result);
    }
}
