<?php
namespace Cms\Controller;

use Application\Utility\DataTable;
use Cms\Form\PostForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Cms\Service\PostServiceInterface;

class ContentController extends AbstractActionController
{
    /**
     * @var \Cms\Service\PostServiceInterface
     */
    protected $postService;
    protected $postForm;

    public function __construct(PostServiceInterface $postService, PostForm $postForm)
    {
        $this->postService = $postService;
        $this->postForm = $postForm;
    }

    public function indexAction()
    {
        $posts = $this->postService->findAllPosts()->toArray();
        $contentTable = new DataTable();
        $contentTable->setData($posts);
        $contentTable->insertLinkButton('/cms/add', 'Neue Seite');
        $contentTable->setColumns(array(
            array(
                'name'  => 'title',
                'label' => 'Seitenname'
            ),
            array(
                'name' => 'url',
                'label' => 'Linkadresse'
            ),
            array (
                'name'  => 'href',
                'label' => 'Aktion',
                'type'  => 'custom',
                'render' => function($row) {
                    $edit = '<a href="/cms/edit/'.$row['id'].'">Edit</a>';
                    $delete = '<a href="/cms/delete/'.$row['id'].'">Delete</a>';
                    return $edit.' '.$delete;
                }
            )
        ));
        
        return new ViewModel(array(
            'posts' => $this->postService->findAllPosts()->toArray(),
            'contentTable' => $contentTable,
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

    public function addAction()
    {
        $this->layout()->setVariable('contentTitle', 'Some value for the variable');
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
    public function deleteAction()
    {
        try {
            $post = $this->postService->findPost($this->params('id'));
        } catch (\InvalidArgumentException $e) {
            return $this->redirect()->toRoute('cms');
        }

        $request = $this->getRequest();

        if ($request->isPost()) {
            $del = $request->getPost('delete_confirmation', 'no');

            if ($del === 'yes') {
                $this->postService->deletePost($post);
            }

            return $this->redirect()->toRoute('cms');
        }

        return new ViewModel(array(
            'post' => $post
        ));
    }
}