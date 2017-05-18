<?php
namespace Application\Controller;



use Application\Form\TestForm;
use Application\Model\Abstracts\Microtime;
use Application\Service\StatisticService;
use Application\Utility\DataTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class SystemController extends AbstractActionController
{
    /** @var  $statsService StatisticService */
    private $statsService;

    public function dashboardAction()
    {
        /** @var  $statsService StatisticService */
        $this->statsService = $this->getServiceLocator()->get('StatisticService');
        /** turn off (slider) sidebar */
        $this->layout()->setVariable('showSidebar', false);
        $top10 = $this->statsService->getMostVisitedPages(10);
        $sysLog = $this->statsService->getSystemLog();
        $sysLogMod = $sysLog;
        foreach ($sysLogMod as $key => $value){
            if ($value->data === null) $replace = '';
            else {
                $replace = implode('---', $value->data);
            }
            $sysLogMod[$key]->data = $replace;
        }
        $sysLogTable = new DataTable(array( 'data' => $sysLogMod ));
        $sysLogTable->prepare();


        return new ViewModel(array(
            'top10'     => $top10,
            'sysLog'    => ($sysLog == null) ? null : array_reverse($sysLog),
            'userStats' => array(
                                array( "Alle Clicks"         => $this->statsService->getPageHits() ),
                                array( "Aktive User"         => count( $this->statsService->getActiveUsers() ) ),
                                array( "meistbesuchter Link" => ( isset($top10[0]) ) ? $top10[0]->url . ' with ' . $top10[0]->hitsSum : null ),
            ),
            'sysLogTable' => $sysLogTable,
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
                $result['users'] = Microtime::addDateTime( $statsService->getActiveUsers($request->microtime) );
                break;
        };

        //output
        return new JsonModel($result);
    }
    public function formtestAction() {

        $form = new TestForm();
        $form->setData(array(
            'Text' => null,
            'Textarea' => null,
            'Number' => null,
            'Password' => null,
            'Range' => null,
            'Url' => null,
            'File' => null,
            'Button' => null,
            'CAPTCHA' => null,
            'Data' => null,
            'DataSelect' => null,
            'DataTime' => null,
            'DateTimeLocal' => null,
            'DateTimeSelect' => null,
            'Month' => null,
            'MonthSelect' => null,
            'Week' => null,
            'Time' => null,
            'Button' => null,
            'Radio' => null,
            'Checkbox' => null,
            'MultiCheckbox' => null,
            'Select' => null,
            'Color' => null,
        ));
        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
        }
        bdump( $form->isValid());
        $form->isValid();
        return array(
            'form' => $form
        );
    }
}
