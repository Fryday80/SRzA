<?php
namespace Cast\Controller;

use Cast\Form\FamilyForm;
use Cast\Utility\FamilyDataTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class FamilyController extends AbstractActionController
{
    public function indexAction() {
        $familyTable = $this->getServiceLocator()->get("Cast\Model\FamiliesTable");
        $famTable = new FamilyDataTable( );
        $famTable->setData($familyTable->getAll());
        $famTable->setButtons('all');
        $famTable->insertLinkButton('/castmanager/families/add', 'add new familiy');
        $famTable->insertLinkButton('/castmanager', 'Zurück');
        return new ViewModel( array(
            'families' => $famTable,
        ) );
    }
    public function addAction() {
        $form = new FamilyForm();
        $form->get('submit')->setValue('add');
        $form->setAttribute('action', '/castmanager/families/add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $familyTable = $this->getServiceLocator()->get("Cast\Model\FamiliesTable");
                $data = $form->getData();
                $familyTable->add($data);
                return $this->redirect()->toRoute('castmanager/families');
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
            return $this->redirect()->toRoute('castmanager/families');
        }
        $familyTable = $this->getServiceLocator()->get("Cast\Model\FamiliesTable");
        if (!$family = $familyTable->getById($id)) {
            return $this->redirect()->toRoute('castmanager/families');
        }
        $form = new FamilyForm();
        $operator = 'Edit';
        $form->get('submit')->setAttribute('value', $operator);
        $form->populateValues($family);
        $form->setAttribute('action', '/castmanager/families/edit/' . $id);

        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $familyTable->save($id, $form->getData());
                return $this->redirect()->toRoute('castmanager/families');
            }
        }
        return array(
            'id' => $id,
            'form' => $form
        );
    }
    public function deleteAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (! $id) {
            return $this->redirect()->toRoute('castmanager/families');
        }
        $familyTable = $this->getServiceLocator()->get("Cast\Model\FamiliesTable");
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $familyTable->remove($id);
            }

            // Redirect to list of Users
            return $this->redirect()->toRoute('castmanager/families');
        }

        return array(
            'id' => $id,
            'family' => $familyTable->getById($id)
        );
    }
}
