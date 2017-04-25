<?php
namespace Application\Controller;



use Application\Model\Abstracts\Microtime;
use Application\Service\StatisticService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class SystemController extends AbstractActionController
{
    /** @var  $statsService StatisticService */
    private $statsService;

    public function dashboardAction()
    {
        $this->layout()->setVariable('showSidebar', false);
        $this->statsService = $this->getServiceLocator()->get('StatisticService');
        $mvL = $this->statsService->getMostVisitedPages();
        $mvL = (isset($mvL[0])) ? $mvL[0]->url . ' with ' . $mvL[0]->hitsSum : null;
        $sysLog = $this->statsService->getSystemLog();
        $sysLog = ($sysLog == null) ? null : array_reverse($sysLog);
        $userStats = array(
            array("All Clicks"    => $this->statsService->getPageHits()),
            array("Aktive User"   => count( $this->statsService->getActiveUsers() )),
            array("meistbesuchter Link"  => $mvL),
        );
        return new ViewModel(array(
            'liveClicks'  => $this->statsService->getActionLog(),
            'activeUsers' => $this->statsService->getActiveUsers(),
            'sysLog'      => $sysLog,
            'userStats'   => $userStats,
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
        $this->statsService = $statsService = $this->getServiceLocator()->get('StatisticService');
        $request = json_decode($this->getRequest()->getContent());
        $result = ['error' => false];
        switch ($request->method) {
            case 'getLiveActions' :
                //@todo check parameter since if exists (dann bei allen hier)
                $result['actions'] = Microtime::addDateTime( $statsService->getActionLog($request->since) );
                break;
            case 'getActiveUsers' :
                //@todo check parameter since if exists (dann bei allen hier)
//                var_dump((int)$request->userId+1);
                $result['users'] = Microtime::addDateTime( $statsService->getActiveUsers($request->userId) );
                break;
        };

        //output
        return new JsonModel($result);
    }
}
