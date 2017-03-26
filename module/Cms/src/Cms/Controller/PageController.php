<?php
namespace Cms\Controller;

use Auth\Service\AccessService;
use Zend\Mvc\Controller\AbstractActionController;
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

     public function indexAction()
     {
         $url = $this->params('title');
         if ($url == '_default') {
             //go to homepage
             $url = 'Home';
         }

         //get page data
         $page = $this->postService->findByUrl($url);
         $exceptRolls = $page->getExceptedRoles(true);
         //check auth
         $role = $this->accessService->getRole();

         if (in_array($role, $exceptRolls)) {
            //deny
             return $this->redirect()->toUrl('/home');
         }
         return new ViewModel(array(
             'page' => $page
         ));
     }
}