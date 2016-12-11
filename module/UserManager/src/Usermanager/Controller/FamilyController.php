<?php
namespace Usermanager\Controller;

use Usermanager\Form\FamilyForm;
use Usermanager\Utility\FamilyDataTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class FamilyController extends AbstractActionController
{
    public function indexAction() {
        $familyTable = $this->getServiceLocator()->get("Usermanager\Model\FamiliesTable");
        $families = $familyTable->getAll();
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
                $familyTable = $this->getServiceLocator()->get("Usermanager\Model\FamiliesTable");
                $data = $form->getData();
                $familyTable->add($data);
                return $this->redirect()->toRoute('families');
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
            return $this->redirect()->toRoute('/families');
        }
        $familyTable = $this->getServiceLocator()->get("Usermanager\Model\FamiliesTable");
        if (!$family = $familyTable->getById($id)) {
            return $this->redirect()->toRoute('/families');
        }
        $form = new FamilyForm();
        $operator = 'Edit';
        $form->get('submit')->setAttribute('value', $operator);
        $form->populateValues($family);
        $form->setAttribute('action', '/families/edit/' . $id);

        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $familyTable->save($id, $form->getData());
                return $this->redirect()->toRoute('families');
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
            return $this->redirect()->toRoute('families');
        }
        $familyTable = $this->getServiceLocator()->get("Usermanager\Model\FamiliesTable");
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $familyTable->remove($id);
            }

            // Redirect to list of Users
            return $this->redirect()->toRoute('families');
        }

        return array(
            'id' => $id,
            'family' => $familyTable->getById($id)
        );
    }
}
