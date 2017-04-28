<?php
namespace Application\Controller;



use Application\Form\TestForm;
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
        $mvL = $this->statsService->getMostVisitedPages(10);
        $mvL1 = (isset($mvL[0])) ? $mvL[0]->url . ' with ' . $mvL[0]->hitsSum : null;
        $sysLog = $this->statsService->getSystemLog();
        $sysLog = ($sysLog == null) ? null : array_reverse($sysLog);
        $userStats = array(
            array("All Clicks"    => $this->statsService->getPageHits()),
            array("Aktive User"   => count( $this->statsService->getActiveUsers() )),
            array("meistbesuchter Link"  => $mvL1),
        );

        return new ViewModel(array(
            'sysLog'    => $sysLog,
            'userStats' => $userStats,
            'top10'     => $mvL,
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
//                var_dump($request->microtime);
//                var_dump( $statsService->getActiveUsers($request->microtime) );
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
