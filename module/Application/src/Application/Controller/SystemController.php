<?php
namespace Application\Controller;

use Application\Form\ContactForm;
use Application\Form\MailTemplatesForm;
use Application\Form\TestForm;
use Application\Model\Enums\Microtime;
use Application\Service\CacheService;
use Application\Service\MessageService;
use Application\Service\StatisticService;
use Application\Service\SystemService;
use Application\Utility\DataTable;
use Exception;
use Media\Service\MediaService;
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
    /** @var CacheService  */
    private $cacheService;

    public function __construct(SystemService $systemService, StatisticService $statisticService, MessageService $messageService, CacheService $cacheService)
    {
        $this->systemService = $systemService;
        $this->statsService = $statisticService;
        $this->messageService = $messageService;
        $this->cacheService = $cacheService;
    }

	public function testAction()
	{
		$dumpLog = array ();
		$log = '';

		/** @var MediaService $mediaService */
        $mediaService = $this->systemService->serviceManager->get('MediaService');
        bdump($mediaService->createThumbsForAll() );

		$msg = 'msg';
		$test = new ContactForm();

		return array (
			'log' => $log,
			'msg' => $msg,
			'test' => $test,
		);
    }

    public function dashboardAction()
    {
        // turn off (slider) sidebar
        $this->layout()->setVariable('showSidebar', false);
        $top10 = $this->statsService->getMostVisitedPages(10);
        $sysLog = $sysLogMod = $this->statsService->getSystemLog();

        if ($sysLogMod !== null) {
			foreach ($sysLogMod as $key => $value) {
				if ($value->data == null) $replace = '';
				elseif (is_array($value->data)) {
					$replace = implode('---', $value->data);
				}
				$sysLogMod[ $key ]->data = $replace;
			}
		}
		$sysLogTable = ($sysLogMod !== null) ? new DataTable(array( 'data' => $sysLogMod )) : new DataTable();
        $sysConf = $this->systemService->getConfig();
        $cacheList = $this->cacheService->getCacheList();
        return new ViewModel(array(
            'systemConfig' => $sysConf,
            'cacheList' => $cacheList,
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
            /** @var MediaService $mediaService */
            $mediaService = $this->systemService->serviceManager->get('MediaService');

            if (true) {
                $fileData = $this->getRequest()->getFiles()->toArray()['File'];
                try {
                    $uploadHandler = $mediaService->uploadHandlerFactory($fileData, '/gallery/pups', true);
                    $uploadHandler->autoRename = true;
                    $uploadHandler->setName("a");
                    $uploadHandler->upload();
                } catch (Exception $e) {
                    bdump($e->getMessage());
                }
            } else {
                //@todo testen
                $filesData = $this->getRequest()->getFiles()->toArray();
                try {
                    //@todo testen -> targetFolder und targetName als string[] übergeben
                    $mediaService->multiUpload($filesData, '/', "neuerName");
                } catch (Exception $e) {
                    bdump($e->getFile());
                    bdump($e->getLine());
                    bdump($e->getMessage());
                }
            }
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
                    $result['cacheList'] = $this->cacheService->getCacheList();
                break;
            case 'clearCache' :
                if (property_exists($request, 'name') ) {
                    $this->cacheService->clearCache($request->name);
                    $result['msg'] = ($this->cacheService->hasCache($request->name))? 'error' : 'success';
                } else {
                    $this->response->setStatusCode(400);
                    $result['error'] = true;
                    $result['msg'] = "need param 'name'!";
                }
                break;
            case 'getSystemConfig':
                $result['systemConfig'] = $this->systemService->getConfig();
                break;
            case 'setSystemConfig' :
                if(property_exists($request, 'valueName') && property_exists($request, 'value')){
                    try {
                        $this->systemService->setConfig($request->valueName, $request->value);
                        $result['msg'] = 'success';
                    } catch (Exception $e) {
                        $this->response->setStatusCode(500);
                        $result['error'] = true;
                        $result['msg'] = $e->getMessage();
                    }
                } else {
                    $this->response->setStatusCode(400);
                    $result['error'] = true;
                    $result['msg'] = "need params 'valueName' and 'value'!";
                }
                break;
            default:
                $this->response->setStatusCode(400);
                $result['error'] = true;
                $result['msg'] = "Method do not exist!";
        };
        //output
        return new JsonModel($result);
    }

	public function jsonGetMissingThumbsAction()
	{
		/** @var MediaService $mediaService */
		$mediaService = $this->systemService->serviceManager->get('MediaService');
		return new JsonModel($mediaService->getMissingThumbs());

    }
	public function jsonRecreateThumbsAction()
	{

	}
    public function maintenanceAction() {
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        return $viewModel;
    }

    public function messageAction()
    {    
        $msg = $this->flashmessenger()->getCurrentMessagesFromNamespace('messagePage');
        
        $msg[0] = (isset ($msg[0]) ) ? $msg[0] : 'Sorry:::No Message Found';
        $msg = explode(':::', $msg[0]);
        $msg[1] = (isset ($msg[1]) ) ? $msg[1] : $msg[0];
        return array(
            'title' => $msg[0],
            'message' => $msg[1]
        );
    }

	public function phpAction()
	{
		return array();
    }
}
