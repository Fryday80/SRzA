<?php
namespace Cast\Controller;

use Auth\Model\UserTable;
use Cast\Form\CharacterForm;
use Cast\Model\CharacterTable;
use Cast\Model\FamiliesTable;
use Cast\Model\JobTable;
use Cast\Utility\CharacterDataTable;
use Exception;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class CharacterController extends AbstractActionController
{
    public function indexAction() {
        $familyTable = $this->getServiceLocator()->get("Cast\Model\CharacterTable");
        $families = $familyTable->getAll();
        $famTable = new CharacterDataTable();
        $famTable->setData($families);
        $famTable->setButtons('all');
        $famTable->insertLinkButton('/castmanager/characters/add', 'neuer Charakter');
        $famTable->insertLinkButton('/castmanager', 'Zurück');
        return new ViewModel(array(
            'families' => $famTable,
        ));
    }

    public function addAction() {
        $form = $this->createCharacterForm();
        $form->get('submit')->setValue('add');
        $form->setAttribute('action', '/castmanager/characters/add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $charTable = $this->getServiceLocator()->get("Cast\Model\CharacterTable");
                $data = $form->getData();
                $charTable->add($data);
                return $this->redirect()->toRoute('castmanager/characters');
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
            return $this->redirect()->toRoute('castmanager/characters');
        }
        $charTable = $this->getServiceLocator()->get("Cast\Model\CharacterTable");
        if (!$character = $charTable->getById($id)) {
            return $this->redirect()->toRoute('castmanager/characters');
        }
        $form = $this->createCharacterForm();
        $operator = 'Edit';
        $form->get('submit')->setAttribute('value', $operator);
        $form->populateValues($character);
        $form->setAttribute('action', '/castmanager/characters/edit/' . $id);

        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $charTable->save($id, $form->getData());
                return $this->redirect()->toRoute('castmanager/characters');
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
            return $this->redirect()->toRoute('castmanager/characters');
        }
        $familyTable = $this->getServiceLocator()->get("Cast\Model\CharacterTable");
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $familyTable->remove($id);
            }

            // Redirect to list of Users
            return $this->redirect()->toRoute('castmanager/characters');
        }

        return array(
            'id' => $id,
            'family' => $familyTable->getById($id)
        );
    }
    function jsonAction() {
        /** @var CharacterTable $charTable */
        $charTable = $this->getServiceLocator()->get("Cast\Model\CharacterTable");
        $request = json_decode($this->getRequest()->getContent());
        $result = ['error' => false];
        try {
            switch ($request->method) {
                case 'getPossibleSupervisors':
                    if (!isset($request->familyID) ) {
                        $result['data'] = false;
                    } else {
                        $result['data'] = $charTable->getByFamilyId($request->familyID);
                    }
                    break;
                case 'getPossibleGuardians':
                    if (!isset($request->familyID) ) {
                        $result['data'] = false;
                    } else {
                        $result['data'] = $charTable->getByFamilyId($request->familyID);
                    }
                    break;
            };
        } catch (Exception $e) {
            $result['error'] = true;
            $result['message'] = $e->getMessage();
        }

        //output
        return new JsonModel($result);
    }
    private function createCharacterForm() {
        /** @var FamiliesTable $familyTable */
        $familyTable = $this->getServiceLocator()->get("Cast\Model\FamiliesTable");
        $families = $familyTable->getAll();
        /** @var UserTable $userTable */
        $userTable = $this->getServiceLocator()->get("Auth\Model\UserTable");
        $users = $userTable->getUsers()->toArray();
        /** @var JobTable $jobTable */
        $jobTable = $this->getServiceLocator()->get("Cast\Model\JobTable");
        $jobs = $jobTable->getAll();
        return new CharacterForm($users, $families, $jobs);
    }
}
