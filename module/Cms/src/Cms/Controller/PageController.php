<?php
namespace Cms\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Cms\Service\PostServiceInterface;

class PageController extends AbstractActionController
{
     /**
      * @var \Cms\Service\PostServiceInterface
      */
     protected $postService;

     public function __construct(PostServiceInterface $postService)
     {
         $this->postService = $postService;
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
         $accessService = $this->getServiceLocator()->get('AccessService');
         $role = $accessService->getRole();

         if (in_array($role, $exceptRolls)) {
            //deny
             return $this->redirect()->toUrl('/home');
         }
         return new ViewModel(array(
             'page' => $page
         ));
     }
}