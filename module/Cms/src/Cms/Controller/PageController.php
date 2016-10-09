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
         return new ViewModel(array(
             'page' => $this->postService->findByUrl($url)
         ));
     }
}