<?php
namespace Application\Controller;


use Application\Model\Action;
use Application\Model\ActionType;
use Application\Model\ActiveUser;
use Application\Model\SystemLog;
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
        $this->statsService = $this->getServiceLocator()->get('StatisticService');
        $userStats = array(
            array("All Clicks"    => $this->statsService->getPageHits()),
            array("Aktive User"   => count( $this->statsService->getActiveUsers() )),
            array("meistbesuchter Link"  => $this->statsService->getMostVisitedPages()[0]->url . ' with ' . $this->statsService->getMostVisitedPages()[0]->hitsSum),
        );
        return new ViewModel(array(
            'liveClicks'  => $this->statsService->getActionLog(),
            'activeUsers' => $this->statsService->getActiveUsers(),
            'sysLog'      => null, // $this->getDataStringFromDataSets( $this->statsService->getSysLog() ),
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
//        var_dump ($request);
        $result = ['error' => false];
        switch ($request->method) {
            case 'getLiveActions':
                //@todo check parameter since if exists (dann bei allen hier)
                $result['actions'] = $this->addDateTime( $statsService->getActionLog($request->since+1));
                $result['users'] = $this->addDateTime( $statsService->getActiveUsers($request->lastUser+1) );
                break;
        };

        //output
        return new JsonModel($result);
    }
    private function addDateTime($itemArray){
        if (!is_array($itemArray)) return null;
        foreach ($itemArray as $item) {
            $item->dateTime = self::dateFromMicrotime($item->time);
        }
        return $itemArray;
    }
    static function dateFromMicrotime($microtime, $format = ('H:i')){
        $t  = substr($microtime, 0 , strlen(time()));
        return date ($format, (int)$t);
    }
}
