<?php
namespace Application\Controller;

use Application\Form\MailTemplatesForm;
use Application\Form\TestForm;
use Application\Model\Abstracts\Microtime;
use Application\Service\MailTemplateService;
use Application\Service\StatisticService;
use Application\Utility\DataTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class SystemController extends AbstractActionController
{
    /** @var  $statsService StatisticService */
    private $statsService;
    private $mailTemplateService;

    public function __construct(StatisticService $statisticService,  MailTemplateService $mailTemplateService)
    {
        $this->statsService = $statisticService;
        $this->mailTemplateService = $mailTemplateService;
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
        $form->isValid();
        return array(
            'form' => $form
        );
    }
    public function mailTemplatesIndexAction() {
        $templates = $this->mailTemplateService->getAllTemplates();
        $templates = $this->refactorMailTemplates($templates);
        $dataTable = new DataTable();
        $dataTable->setData($templates);
        $dataTable->insertLinkButton('/system/mailTemplates/add', 'Neu');
        return array(
          'data' => $dataTable,
        );
    }
    public function mailTemplateAction() {
        $templateID = $this->params()->fromRoute('templateName');
        if ($templateID == 'add'){
            $vars = $this->addMailTemplate();
        }
        elseif ($templateID == 'delete'){
            $vars = $this->deleteMailTemplate($templateID);
        }
        else{
            $vars = $this->editMailTemplate($templateID);
        }
        return $vars;
    }

    private function addMailTemplate($name = null)
    {
        $form = new MailTemplatesForm();
        $form->get('submit')->setValue('Add');
        $request = $this->getRequest();
        if($request->isPost()){
            $form->setData($request->getPost()->toArray());
            if ($form->isValid()){
                $post = $form->getData();
                unset($post['submit']);
            $this->mailTemplateService->save($post);
                return $this->redirect()->toRoute('system/mailTemplates');
            }
        }

//        if (!$name === null) $form->get('id')->setValue($name);
        $vars = array(
            'form'  => $form,
        );
        return $vars;
    }

    private function editMailTemplate($templateID)
    {
        $form = new MailTemplatesForm();
        $template = $this->mailTemplateService->getByID($templateID);
        $form->setData($template);
        $form->get('submit')->setValue('Edit');
        $request = $this->getRequest();
        if($request->isPost()){
            $form->setData($request->getPost()->toArray());
            if ($form->isValid()){
                $post = $form->getData();
                unset($post['submit']);
                $this->mailTemplateService->save($post);
                return $this->redirect()->toRoute('system/mailTemplates');
            }
        }
        return array(
          'template' => $template,
            'form'  => $form,
        );
    }

    private function deleteMailTemplate($templateID)
    {
        //@todo comfirm page
        $this->mailTemplateService->deleteByID($templateID);
        return $this->redirect()->toRoute('system/mailTemplates');
    }

    private function refactorMailTemplates($templates)
    {
        $i = 0;
        while( isset($templates[$i]) ){
            //add links
            if ($templates[$i]['build_in'] !== "1") {
                $templates[$i]['Aktion'] = '<a href="/system/mailTemplates/' . $templates[$i]['id'] . '">Edit</a><br/>';
                $templates[$i]['Aktion'] .= '<a href="/system/mailTemplates/delete">delete</a> ';
            }
            else {
                $templates[$i]['Aktion'] = '<a href="/system/mailTemplates/' . $templates[$i]['id'] . '">Edit</a><br/>';
            }
            // get variables
            $from = '{{';
            $to = '}}';
            $aMatches = array();
            preg_match_all("/\\".$from."(.*?)\\".$to."/", $templates[$i]['msg'], $aMatches);
            $templates[$i]['variables'] = implode (' <br/>', $aMatches[1]);
            // remove
            unset ($templates[$i]['build_in']);
            //next
            $i++;
        }
        return $templates;
    }
}
