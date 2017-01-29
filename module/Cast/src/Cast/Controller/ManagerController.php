<?php
namespace Cast\Controller;

use Cast\Form\FamilyForm;
use Cast\Utility\FamilyDataTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Utility\DataTable;

class ManagerController extends AbstractActionController
{
    public function indexAction() {
        //fine presentation of the cast
        $familyTable = $this->getServiceLocator()->get("Cast\Model\FamiliesTable");
        $families = $familyTable->getAll();
        $jobTable = $this->getServiceLocator()->get("Cast\Model\JobTable");
        $jobs = $jobTable->getAll();
        $characterTable = $this->getServiceLocator()->get("Cast\Model\CharacterTable");
        $characters = $characterTable->getAll();
        $data = array(
            'data' => array (
                0 => array (
                    'id'=> '1',
                    'name'=>'families ('.count($families).')',
                    'link' => 'families'
                ),
                1 => array (
                    'id'=> '2',
                    'name'=>'jobs ('.count($jobs).')',
                    'link' => 'jobs'
                ),
                2 => array (
                    'id'=> '3',
                    'name'=>'characters ('.count($characters).')',
                    'link' => 'characters'
                )
            ),
            'columns' =>    array(
                array (
                    'name'  => 'href',
                    'label' => 'Gruppen',
                    'type'  => 'custom',
                    'render' => function($row) {
                        $edit = '<a href="/castmanager/'.$row['link'].'">'.$row['name'].'</a>';
                        return $edit;
                    }
                )
            ),
        );

        $dataTable = new DataTable($data);
        $dataTable->setButtons('all');
        return array(
            'dataTable' => $dataTable,
        );
    }
    public function addAction() {
        $form = new FamilyForm();
        $form->get('submit')->setValue('add');
        $form->setAttribute('action', '/families/add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $familyTable = $this->getServiceLocator()->get("Cast\Model\FamiliesTable");
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
        $familyTable = $this->getServiceLocator()->get("Cast\Model\FamiliesTable");
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
        $familyTable = $this->getServiceLocator()->get("Cast\Model\FamiliesTable");
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
