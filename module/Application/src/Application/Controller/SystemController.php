<?php
namespace Application\Controller;

use Application\Form\MailTemplatesForm;
use Application\Form\TestForm;
use Application\Model\Abstracts\Microtime;
use Application\Service\MessageService;
use Application\Service\StatisticService;
use Application\Service\SystemService;
use Application\Utility\DataTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class SystemController extends AbstractActionController
{
    /** @var SystemService */
    private $systemService;
    /** @var MessageService */
    private $messageService;
    /** @var StatisticService */
    private $statsService;

    public function __construct(SystemService $systemService, StatisticService $statisticService,  MessageService $messageService)
    {
        $this->systemService = $systemService;
        $this->statsService = $statisticService;
        $this->messageService = $messageService;
    }
    public function dashboardAction()
    {
        // turn off (slider) sidebar
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
        $form->isValid();
        return array(
            'form' => $form
        );
    }
    public function mailTemplatesIndexAction() {
        $templates = $this->messageService->getAllTemplates();
        //refactor data:
        $i = 0;
        while( isset($templates[$i]) ){
            //add links
            $edit = $templates[$i]['name'];
            $templates[$i]['Aktion'] = '<a href="/system/mailTemplates/' . $edit . '">Edit</a><br/>';
            //next
            $i++;
        }

        $dataTable = new DataTable();
        $dataTable->setData($templates);
        $dataTable->insertLinkButton('/system/mailTemplates/add', 'Neu');
        return array(
          'data' => $dataTable,
        );
    }
    public function mailTemplateAction() {
        $templateName = $this->params()->fromRoute('templateName');
        $form = new MailTemplatesForm();
        $template = $this->messageService->getTemplateByName($templateName);
        if($template == null) return $this->addMailTemplate($templateName);
        $form->setData($template);
        $form->get('submit')->setValue('Edit');
        $request = $this->getRequest();
        if($request->isPost()){
            $form->setData($request->getPost()->toArray());
            if ($form->isValid()){
                $post = $form->getData();
                $this->messageService->saveTemplate($post);
                return $this->redirect()->toRoute('system/mailTemplates');
            }
        }
        return array(
            'form'  => $form,
            'back' => '<a href = "/system/mailTemplates" ><button>Nein, Zurück</button></a>',
        );
    }
    public function jsonAction() {
        /** @var  $statsService StatisticService */
        $statsService = $this->statsService;
        $request = json_decode($this->getRequest()->getContent());
        $result = ['error' => false];
        if (!property_exists($request, 'method') ) {
            $this->response->setStatusCode(400);
            $result['error'] = true;
            $result['msg'] = "need param 'method'!";
            return new JsonModel($result);
        }
        switch ($request->method) {
            case 'getLiveActions' :
                if (property_exists($request, 'since') ) {
                    $result['actions'] = Microtime::addDateTime( $statsService->getActionLog($request->since) );
                } else {
                    $this->response->setStatusCode(400);
                    $result['error'] = true;
                    $result['msg'] = "need param 'since'!";
                }
                break;
            case 'getActiveUsers' :
                if (property_exists($request, 'microtime') ) {
                    $result['users'] = Microtime::addDateTime( $statsService->getActiveUsers($request->microtime) );
                } else {
                    $this->response->setStatusCode(400);
                    $result['error'] = true;
                    $result['msg'] = "need param 'microtime'!";
                }
                break;
            case 'getCacheStats' :
//                if (property_exists($request, 'microtime') ) {
//                    $result['users'] = Microtime::addDateTime( $statsService->getActiveUsers($request->microtime) );
//                } else {
//                    $this->response->setStatusCode(400);
//                    $result['error'] = true;
//                    $result['msg'] = "need param 'microtime'!";
//                }
//                break;
            case 'clearCache' :
//                if (property_exists($request, 'microtime') ) {
//                    $result['users'] = Microtime::addDateTime( $statsService->getActiveUsers($request->microtime) );
//                } else {
//                    $this->response->setStatusCode(400);
//                    $result['error'] = true;
//                    $result['msg'] = "need param 'microtime'!";
//                }
//                break;
        };

        //output
        return new JsonModel($result);
    }
}
