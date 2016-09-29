<?php
namespace Cms\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Cms\Service\PostServiceInterface;

class ListController extends AbstractActionController
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
         return new ViewModel(array(
             'posts' => $this->postService->findAllPosts()
         ));
     }
     public function detailAction()
     {
         $id = $this->params()->fromRoute('id');

         try {
             $post = $this->postService->findPost($id);
         } catch (\InvalidArgumentException $ex) {
             //redirect if page not found
             return $this->redirect()->toRoute('cms');
         }

         return new ViewModel(array(
             'post' => $post
         ));
     }
}