<?php
namespace Application\Controller;


use Application\Model\ActionsLog;
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
        $quickLinks = array(
            array( "<a href='/'> Home</a>"),
            array( "<a href='/cms'> Content</a>"),
            array( "<a href='/media/filebrowser'> File Browser</a>"),
            array( "<a href='/nav/sort'> Navigation</a>"),
            array( "<a href='/system/dashboard'> Dashboard Reload</a>"),
        );
        $userStats = array(
            array("All Clicks"    => $this->statsService->getAllHits()),
            array("Aktive User"   => count( $this->statsService->getActiveUsers() )),
            array("meistbesuchter Link"  => $this->statsService->getMostVisitedPages()[0]['url'] . ' with ' .$this->statsService->getMostVisitedPages()[0]['hits']),
        );
        return new ViewModel(array(
            'quickLinks'  => $this->getDataStringFromDataSets( $quickLinks ),
            'liveClicks'  => $this->getDataStringFromDataSets( $this->statsService->getActionsLog() ),
            'activeUsers' => $this->getDataStringFromDataSets( $this->statsService->getActiveUsers() ),
            'sysLog'      => $this->getDataStringFromDataSets( $this->statsService->getSysLog() ),
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
        $this->statsService = $statsService = $this->getServiceLocator()->get('StatisticService');
        $request = json_decode($this->getRequest()->getContent());
        $result = ['error' => false];
        switch ($request->method) {
            case 'getLiveActions':
                //@todo check parameter since if exists (dann bei allen hier)
                $result['actions'] = $this->getDataStringFromDataSets( $statsService->getActionsLogByIDAndTime($request->actionID, $request->since));
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
        if (! isset( $data[0] ) ) return null;
        if ($data[0] instanceof ActionsLog){
            bdump(($data[0] instanceof ActionsLog));
            bdump($data);
            /** @var  $item ActionsLog*/
            foreach ($data as $item)
                if ($item !== null) {
                    $insideString = '';
                    $insideString .= $item->actionType . '<b> @ </b>' . date('H:i', $item->time) . '<b>: </b>' .
                        $item->msg . '<b> of </b>' . $item->title . '<b> from </b>' . $item->data['userName'];
                    array_push($result, array("string" => $insideString, "time" => $item->time, "id" =>  $item->itemId));
                }
        return $result;
        }
        if ($data[0] instanceof ActiveUser){
            /** @var  $item ActiveUser*/
            foreach ($data as $item)
                if ($item !== null) {
                    $insideString = '';
                    $insideString .= "$item->userName: $item->url <b> @ </b>" . date('H:i', $item->time);
                    array_push($result, array("string" => $insideString));
                }
        return $result;
        }
        if ($data[0] instanceof SystemLog){
            $count = $this->statsService->getNumberOfLogs();
            /** @var  $item SystemLog*/
            foreach ($data as $item)
                if ($item !== null) {
                    $insideString = '';
                    $insideString .= "<li>$item->msg total count $count</li>";
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
