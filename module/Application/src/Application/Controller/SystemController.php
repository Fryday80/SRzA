<?php
namespace Application\Controller;


use Application\Model\ActionsLog;
use Application\Model\ActiveUser;
use Application\Model\StatisticDataCollection;
use Application\Model\SystemLog;
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
        $quickLinks = array(
            array( "<a href='/'> Home</a>"),
            array( "<a href='/cms'> Content</a>"),
            array( "<a href='/media/filebrowser'> File Browser</a>"),
            array( "<a href='/nav/sort'> Navigation</a>"),
            array( "<a href='/system/dashboard'> Dashboard Reload</a>"),
        );
        $userStats = array(
            array("All Clicks"    => $statsService->getAllHits()),
            array("Aktive User"   => count( $statsService->getActiveUsers() )),
            array("meistbesuchter Link"  => $statsService->getMostVisitedPages()[0]['url']),
        );
        
        return new ViewModel(array(
            'quickLinks'  => $this->getDataStringFromDataSets( $quickLinks ),
            'liveClicks'  => $this->getDataStringFromDataSets( $statsService->getActionsLog() ),
            'activeUsers' => $this->getDataStringFromDataSets( $statsService->getActiveUsers() ),
            'sysLog'      => $this->getDataStringFromDataSets( $statsService->getSysLog() ),
            'userStats'   => $this->getDataStringFromDataSets( $userStats ),
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
                //@todo check parameter since if exists (dann bei allen hier)
//                var_dump(json_encode($this->getDataStringFromDataSets( $statsService->actionsLogGetByIDAndTime($request->actionID, $request->since))));
//                die;
//                var_dump($this->getDataStringFromDataSets( $statsService->actionsLogGetByIDAndTime($request->actionID, $request->since)));
                $result['actions'] = $this->getDataStringFromDataSets( $statsService->getActionsLogGetByIDAndTime($request->actionID, $request->since));
                break;
        };

        //output
        return new JsonModel($result);
    }
    private function getDataStringFromDataSets($data){
        $result = array();
        $insideString = '';
        $time = 0;
        $id = 0;
        if (!is_array($data)) return null;
        if ($data == null) return null;
        if ($data[0] instanceof ActionsLog){
            foreach ($data as $item)
                if ($item !== null) {
                    $insideString = '';
                    $time = $item->time;
                    $id = $item->actionID;
                    $insideString .= $item->actionType . '<b> @ </b>' . date('H:i', $item->time) . '<b>: </b>' .
                        $item->msg . '<b> of </b>' . $item->title . '<b> from </b>' . $item->data['userName'];
                    array_push($result, array("string" => $insideString, "time" => $time, "id" => $id));
                }
        return $result;
        }
        if ($data[0] instanceof ActiveUser){
            foreach ($data as $item)
                if ($item !== null) {
                    $insideString = '';
                    $insideString .= "$item->userName: $item->lastActionUrl <b> @ </b>" . date('H:i', $item->time);
                    array_push($result, array("string" => $insideString));
                }
        return $result;
        }
        if ($data[0] instanceof SystemLog){
            foreach ($data as $item)
                if ($item !== null) {
                    $insideString = '';
                    $insideString .= '';
                    array_push($result, array("string" => $insideString));
                }
        return $result;
        }
        else {
            $insideString = "<table>";
            $td = "<td style='width:15%'>";
            $space = "<td style='width:2%'> | </td>";
            $i = 1;
            $c = 0;
            foreach ($data as $value)
                if ($value !== null)
                    foreach ($value as $k => $v) {
                        $insideString .= ($i == 0) ? "<tr>" : "";
                        if (is_int($k)) {
                            $insideString .= "$td $v</td>";
                            $c = 4;
                        }
                        else {
                            $insideString .= "$td<b>$k:</b></td>$td$v</td>";
                            $c = 3;
                        }
                        $insideString .= ($i == $c) ? "</tr>" : "$space";
                        $i++;
                        $i = ($i == ($c+1)) ? 0 : $i;
                    }
            $insideString .= "</table>";
            array_push($result, array("string" => $insideString, "time" => $time, "id" => $id));
        }
        return $result;
    }
}
