<?php
namespace Application\Controller;


use Application\Model\DataObjects\DashboardDataCollection;
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
        $dashboardData = new DashboardDataCollection( $this->getServiceLocator() );
        $dashboardData->setActionLog();
        $dashboardData->setActiveUsers();
        $activeUsers = $dashboardData->getActiveUsers();
        $userStats = array(
            array ( "All Clicks", 42424242),
            array ('Clicks', 42),
            array ( "Aktive User", count( $activeUsers->toArray() )),
            array ( "Data", "you want"),
        );
        
        return new ViewModel(array(
            'dashboardData' => $dashboardData,
            'userStats' => $userStats,
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
        var_dump($request);
        $result = ['error' => false];
        switch ($request->method) {
            case 'getLiveActions':
                //@todo check parameter since if exists (dann bei allen hier)
                $result['actions'] = $statsService->getLastActions()->getJSonUpdate($request->action_id,$request->since);
                break;
        };

        //output
        return new JsonModel($result);
    }
}
