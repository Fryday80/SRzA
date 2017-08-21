<?php
namespace Cms\Controller;

use Application\Utility\DataTable;
use Cms\Form\ContentForm;
use Cms\Form\contentForm;
use Cms\Service\ContentService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Cms\Service\contentServiceInterface;

class ContentController extends AbstractActionController
{
    /** @var ContentService  */
    protected $contentService;
    /** @var ContentForm  */
    protected $contentForm;

    public function __construct(ContentService $contentService, ContentForm $contentForm)
    {
        $this->contentService = $contentService;
        $this->contentForm = $contentForm;
    }

    public function indexAction()
    {
        $posts = $this->contentService->findAllPosts()->toArray();
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
            'posts' => $this->contentService->findAllPosts()->toArray(),
            'contentTable' => $contentTable,
        ));
    }

    public function detailAction()
    {
        $id = $this->params()->fromRoute('id');

        try {
            $post = $this->contentService->findPost($id);
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
            $this->contentForm->setData($request->getPost());
            if ($this->contentForm->isValid()) {
                try {
                    $this->contentService->savePost($this->contentForm->getData());

                    return $this->redirect()->toRoute('cms');
                } catch (\Exception $e) {
                    die($e->getMessage());
                }
            }
        }

        return array(
            'form' => $this->contentForm
        );
    }

    public function editAction()
    {
        $request = $this->getRequest();
        $post = $this->contentService->findPost($this->params('id'));

        $this->contentForm->bind($post);

        if ($request->isPost()) {
            $this->contentForm->setData($request->getPost());

            if ($this->contentForm->isValid()) {
                try {
                    $this->contentService->savePost($post);

                    return $this->redirect()->toRoute('cms');
                } catch (\Exception $e) {
                    die($e->getMessage());
                }
            }
        }

        return array(
            'form' => $this->contentForm
        );
    }
    public function deleteAction()
    {
        try {
            $post = $this->contentService->findPost($this->params('id'));
        } catch (\InvalidArgumentException $e) {
            return $this->redirect()->toRoute('cms');
        }

        $request = $this->getRequest();

        if ($request->isPost()) {
            $del = $request->getPost('delete_confirmation', 'no');

            if ($del === 'yes') {
                $this->contentService->deletePost($post);
            }

            return $this->redirect()->toRoute('cms');
        }

        return new ViewModel(array(
            'post' => $post
        ));
    }
}