<?php
namespace Application\Controller;


use Application\Model\StatisticDataCollection;
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
        /** @var  $dashboardData \Application\Model\StatisticDataCollection*/
        $dashboardData = $statsService->getDataCollection();
        $userStats = array(
            array ( "All Clicks", $dashboardData->getAllHits()),
            array ( "Aktive User", count( $dashboardData->activeUsersSet->getActiveUsers() )),
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
//        var_dump($this->getRequest());
//        var_dump($request);
        $result = ['error' => false];
        switch ($request->method) {
            case 'getLiveActions':
//                var_dump($statsService->getLastActions()->getJSonUpdate($request->actionID, $request->since));  //alter wie gesagt hier kannst du nur direkt ausgeben
                //@todo check parameter since if exists (dann bei allen hier)
                $result['actions'] = $statsService->getDataCollection()->actionsLogSet->getJSonUpdate($request->actionID, $request->since); //fry aber das hier ist die JS var... die hieß immer schon so
                break;
        };

        //output
        return new JsonModel($result);
    }
}
