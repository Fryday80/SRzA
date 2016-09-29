<?php
namespace Cms\Controller;

use Cms\Service\PostServiceInterface;
use Zend\Form\FormInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Helper\ViewModel;

class WriteController extends AbstractActionController
{

    protected $postService;

    protected $postForm;

    public function __construct(PostServiceInterface $postService, FormInterface $postForm)
    {
        $this->postService = $postService;
        $this->postForm = $postForm;
    }

    public function addAction()
    {
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            $this->postForm->setData($request->getPost());
            
            if ($this->postForm->isValid()) {
                try {
                    $this->postService->savePost($this->postForm->getData());
                    
                    return $this->redirect()->toRoute('cms');
                } catch (\Exception $e) {
                    die($e->getMessage());
                }
            }
        }
        
        return array(
            'form' => $this->postForm
        );
    }

    public function editAction()
    {
        $request = $this->getRequest();
        $post = $this->postService->findPost($this->params('id'));
        
        $this->postForm->bind($post);
        
        if ($request->isPost()) {
            $this->postForm->setData($request->getPost());
            
            if ($this->postForm->isValid()) {
                try {
                    $this->postService->savePost($post);
                    
                    return $this->redirect()->toRoute('cms');
                } catch (\Exception $e) {
                    die($e->getMessage());
                }
            }
        }
        
        return array(
            'form' => $this->postForm
        );
    }
}