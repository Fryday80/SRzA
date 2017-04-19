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
        $dashboardData = $statsService->getDataCollection();
        $quickLinks = array(
            array( "<a href='/'> Home</a>"),
            array( "<a href='/system/dashboard'> Dashboard Reload</a>"),
        );
        $userStats = array(
            array("All Clicks"    => $dashboardData->getAllHits()),
            array("Aktive User"   => count( $dashboardData->activeUsersSet->getActiveUsers() )),
            array("Data"  => "you want"),
        );
        
        return new ViewModel(array(
            'quickLinks'  => $this->getDataStringFromDataSets( $quickLinks ),
            'liveClicks'  => $this->getDataStringFromDataSets( $dashboardData->actionsLogToArray() ),
            'activeUsers' => $this->getDataStringFromDataSets( $dashboardData->getActiveUsers() ),
            'sysLog'      => $this->getDataStringFromDataSets( $dashboardData->getSystemLog() ),
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
                $result['actions'] = $this->getDataStringFromDataSets( $statsService->actionsLogGetByIDAndTime($request->actionID, $request->since));
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
        bdump($data);
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
                    $time = $item->time;
                    $id = $item->actionID;
                    $insideString .= $item->actionType . '<b> @ </b>' . date('H:i', $item->time) . '<b>: </b>' .
                        $item->msg . '<b> of </b>' . $item->title . '<b> from </b>' . $item->data['userName'];
                    array_push($result, array("string" => $insideString, "time" => $time, "id" => $id));
                }
        return $result;
        }
        else {
            $insideString = "<table>";
            $td = "<td style='width:15%'>";
            $space = "<td style='width:2%'>&nbsp</td>";
            $i = 0;
            foreach ($data as $value)
                if ($value !== null)
                    foreach ($value as $k => $v) {
                        $insideString .= ($i == 0) ? "<tr>" : "";
                        if (is_int($k)) {
                            $insideString .= "$td $v</td>";
                            $c = 6;
                        }
                        else {
                            $insideString .= "$td<b>$k:</b></td>$td$v</td>";
                            $c = 3;
                        }
                        $insideString .= ($i == ($c-1)) ? "</tr>" : "$space";
                        $i++;
                        $i = ($i == $c) ? 0 : $i;
                    }
            $insideString .= "</table>";
            array_push($result, array("string" => $insideString, "time" => $time, "id" => $id));
        }
        return $result;
    }
}
