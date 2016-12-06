<?php
namespace Usermanager\Controller;

use Usermanager\Model\FamiliesTable;
use Usermanager\Form\FamilyForm;
use Usermanager\Utility\FamilyDataTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class FamilyController extends AbstractActionController
{
    /* @var $familyTable FamiliesTable */
    private $familyTable;

    public function __construct() {
    }

    public function indexAction() {
        $this->familyTable = $this->getServiceLocator()->get("Usermanager\Model\FamiliesTable");
        $families = $this->familyTable->getAll();
        $famTable = new FamilyDataTable();
        $famTable->setData($families);
        return new ViewModel(array(
            'families' => $famTable,
        ));
    }
    public function addAction() {
        $form = new FamilyForm();
        $form->get('submit')->setValue('add');
        $form->setAttribute('action', '/families/add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->familyTable= $this->getServiceLocator()->get("Usermanager\Model\FamiliesTable");
                $data = $form->getData();
                $this->familyTable->change($data['is'], $data);
                $this->galleryService->storeAlbum ($form->getData());
                return $this->redirect()->toRoute('album');
            }
        }
        return array(
            'form' => $form
        );
    }

    public function editAction(){
        $request = $this->getRequest();
        $id = (int) $this->params()->fromRoute('id', null);
        if (! $id && !$request->isPost()) {
            return $this->redirect()->toRoute('/album');
        }
        $album = null;
        try {
            $album = $this->galleryService->getAlbumByID($id);
        } catch (\Exception $ex) {
            print ($ex);
            return $this->redirect()->toRoute('/album');
        }
        $form = new AlbumForm();
        $operator = 'Edit';
        $form->get('submit')->setAttribute('value', $operator);

        $album = $this->addDate($album);

        $form->populateValues($album[0]);

        $form->setAttribute('action', '/album/edit/' . $id);

        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->galleryService->storeAlbum ($form->getData());
                return $this->redirect()->toRoute('album');
            }
            //dump ('not valid'); //cleanfix bugfix
        }
        return array(
            'id' => $id,
            'form' => $form
        );
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        $request = $this->getRequest();
        if (! $id && !$request->isPost()) {
            return $this->redirect()->toRoute('album');
        }
        if ($request->isPost()) {
            $confirmed = $request->getPost('delete_confirm', 'no');
            if ($confirmed !== 'no') {
                $this->galleryService->deleteWholeAlbum($id);
                return $this->redirect()->toRoute('album');
            }
            // Redirect to list of albums
            return $this->redirect()->toRoute('album');
        }
        $form = new ConfirmForm();
        $form->get('id')->setAttribute('value', $id);
        $form->setAttribute('action', '/album/delete/' . $id);

        try {
            $album = $this->galleryService->getAlbumByID($id);
        } catch (\Exception $ex) {
            print ($ex);
            return $this->redirect()->toRoute('/album');
        }
        $event = $album[0]['event'];

        return array (
            'id' => $id,
            'event' => $event,
            'form' => $form
        );
    }
}
