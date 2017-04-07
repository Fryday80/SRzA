<?php
namespace Cms\Controller;

use Auth\Service\AccessService;
use Cms\Model\Post;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Cms\Service\PostServiceInterface;

class PageController extends AbstractActionController
{
     /**
      * @var \Cms\Service\PostServiceInterface
      */
     protected $postService;
     protected $accessService;

     public function __construct(PostServiceInterface $postService, AccessService $accessService)
     {
         $this->postService = $postService;
         $this->accessService = $accessService;
     }

    /**
     * die hier baut ja im endeffect des html zusammen ->dann kommt noch des layout drum rum und des wird zum client geschickt
     *
     *
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
          * @var $page Post
          */
         $page = $this->postService->findByUrl($url);
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