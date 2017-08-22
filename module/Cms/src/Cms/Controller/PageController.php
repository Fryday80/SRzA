<?php
namespace Cms\Controller;

use Auth\Service\AccessService;
use Cms\Model\DataModels\Content;
use Cms\Service\ContentService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class PageController extends AbstractActionController
{
     /** @var ContentService */
     protected $contentService;
     /** @var AccessService  */
     protected $accessService;

     public function __construct(ContentService $contentService, AccessService $accessService)
     {
         $this->contentService = $contentService;
         $this->accessService  = $accessService;
     }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
     public function indexAction()
     {
         $success = true;
         $errorMsg = null;
         $url = $this->params('title');
         if ($url == '_default') {
             //go to homepage
             $url = 'Home';
         }

         //get page data
         /**
          * @var $page Content
          */
         $page = $this->contentService->getByUrl($url);
         if (!$page) {
             $this->getResponse()->setStatusCode(404);
             return;
         }
         $exceptRolls = $page->getExceptedRoles(true);
         //check auth
         $role = $this->accessService->getRole();


         if ($this->getRequest()->isXmlHttpRequest()) {
             if (in_array($role, $exceptRolls)) {
                 $success = false;
                 $errorMsg = 'No Permission';
             }
             $result = new JsonModel(array(
                 'title'    => $page->getTitle(),
                 'content'  => $page->getContent(),
                 'success'  => $success,
                 'error'    => $errorMsg
             ));

             return $result;
         } else {
             if (in_array($role, $exceptRolls)) {
                 return $this->redirect()->toUrl('/home');
             }
             return new ViewModel(array(
                 'page' => $page,
             ));
         }
     }
}