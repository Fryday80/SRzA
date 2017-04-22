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
//            array("meistbesuchter Link"  => $this->statsService->getMostVisitedPages()[0]['url'] . ' with ' . $this->statsService->getMostVisitedPages()[0]['hits']),
        );
        return new ViewModel(array(
            'liveClicks'  => $this->getDataStringFromDataSets( $this->statsService->getActionLog() ),
            'activeUsers' => $this->getDataStringFromDataSets( $this->statsService->getActiveUsers() ),
            'sysLog'      => null, // $this->getDataStringFromDataSets( $this->statsService->getSysLog() ),
            'userStats'   => $this->getDataStringFromArray( $userStats ),
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
    private function getDataStringFromDataSets( $itemArray ){
        if (!is_array($itemArray)) return null;
        $result = array();
        foreach ($itemArray as $key => $value){
            $firstItem = $key;
            break;
        }
        if ($itemArray[$firstItem] instanceof Action){
            /** @var  $item Action*/
            foreach ($itemArray as $item)
                if ($item !== null) {
                    bdump($item);
                    $insideString = '';
                    $insideString .= $this->actionTypeTranslator($item->actionType) . '<b> @ </b>' . $this->dateFromMicrotime($item->time, 'H:i d.m.Y') . '<b>: </b>' .
                        $item->msg . '<b> of </b>' . $item->title . '<b> from </b>' . $item->userName;
                    array_push( $result, array( "string" => $insideString, "time" => $item->time ) );
                }
        return $result;
        }
        if ($itemArray[$firstItem] instanceof ActiveUser){
            /** @var  $item ActiveUser*/
            foreach ($itemArray as $item)
                if ($item !== null) {
                    bdump($item);
                    $insideString = '';
                    $insideString .= "$item->userName: $item->url <b> @ </b>" . $this->dateFromMicrotime($item->time);
                    array_push($result, array("string" => $insideString));
                }
        return $result;
        }
        if ($itemArray[$firstItem] instanceof SystemLog) {
            // @todo
        return $result;
        }
        else return null;
    }
    private function getDataStringFromArray($data){
        if (!is_array($data)) return null;
        $result = array();
        $insideString = "<table>";
        $td = "<td style='width:15%'>";
        $space = "<td style='width:2%'> | </td>";
        $i = 1;
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
        array_push($result, array("string" => $insideString));
        return $result;
    }
    private function dateFromMicrotime($microtime, $format = ('H:i')){
        $t  = substr($microtime, 0 , strlen(time()));
//        $t = (int)explode(".", $microtime/1000)[0];
//        bdump($t);
//        bdump($format);
        return date ($format, (int)$t);
    }
    public function actionTypeTranslator($type){
        if ( $type == ActionType::PAGE_CALL )
            return 'Page Call';
        if ( $type == ActionType::ERROR )
            return 'Error';
    }
}
