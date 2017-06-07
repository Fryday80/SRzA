<?php
namespace Cast\Controller;

use Cast\Form\FamilyForm;
use Cast\Model\FamiliesTable;
use Cast\Service\BlazonService;
use Cast\Utility\FamilyDataTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class FamilyController extends AbstractActionController
{
    /** @var FamiliesTable $familiesTable */
    private $familiesTable;
    /** @var  BlazonService */
    private $blazonService;

    public function __construct(FamiliesTable $familiesTable, BlazonService $blazonService) {
        $this->familiesTable = $familiesTable;
        $this->blazonService = $blazonService;
    }

    public function indexAction() {
        $famTable = new FamilyDataTable( );
        $famTable->setData($this->familiesTable->getAll());
        $famTable->setButtons('all');
        $famTable->insertLinkButton('/castmanager/families/add', 'add new familiy');
        $famTable->insertLinkButton('/castmanager', 'Zurück');
        return new ViewModel( array(
            'families' => $famTable,
        ) );
    }
    public function addAction() {
        $form = new FamilyForm($this->blazonService->getAll());
        $form->get('submit')->setValue('add');
        $form->setAttribute('action', '/castmanager/families/add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            //merge post data and files
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            $form->setData($post);
            if ($form->isValid()) {
                $data = $form->getData();
                //cleanfix
//bdump($data);
//                $this->familiesTable->add($data);
//                return $this->redirect()->toRoute('castmanager/families');
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
        if (!$family = $this->familiesTable->getById($id)) {
            return $this->redirect()->toRoute('castmanager/families');
        }
        $form = new FamilyForm($this->blazonService->getAll());
        $operator = 'Edit';
        $form->get('submit')->setAttribute('value', $operator);
        $form->populateValues($family);
        $form->setAttribute('action', '/castmanager/families/edit/' . $id);

        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->familiesTable->save($id, $form->getData());
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
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->familiesTable->remove($id);
            }

            // Redirect to list of Users
            return $this->redirect()->toRoute('castmanager/families');
        }

        return array(
            'id' => $id,
            'family' => $this->familiesTable->getById($id)
        );
    }
}
