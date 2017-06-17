<?php
namespace Cast\Controller;

use Auth\Service\AccessService;
use Auth\Service\UserService;
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
    /** @var CharacterTable $characterTable */
    private $characterTable;
    /** @var JobTable $jobTable */
    private $jobTable;
    /** @var FamiliesTable $familiesTable */
    private $familiesTable;
    /** @var UserService  */
    private $userService;
    /** @var AccessService  */
    private $accessService;

    public function __construct(CharacterTable $characterTable,
                                JobTable $jobTable,
                                FamiliesTable $familiesTable,
                                AccessService $accessService,
                                UserService $userService
    )
    {
        $this->characterTable = $characterTable;
        $this->jobTable = $jobTable;
        $this->familiesTable = $familiesTable;
        $this->accessService = $accessService;
        $this->userService = $userService;
    }

    public function indexAction() {
        $families = $this->familiesTable->getAll();
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
                $data = $form->getData();
                $this->characterTable->add($data);
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
        if (!$character = $this->characterTable->getById($id)) {
            return $this->redirect()->toRoute('castmanager/characters');
        }
        bdump($character);
        $form = $this->createCharacterForm();
        $operator = 'Edit';
        $form->isBackend();
        $form->get('submit')->setAttribute('value', $operator);

        $possibleGuardians = $this->characterTable->getByFamilyId($character['family_id']);
        $possibleSupervisors = $this->characterTable->getAllPossibleSupervisorsFor($character['tross_id']);

        $form->setPossibleGuardians($possibleGuardians);
        $form->setPossibleSupervisors($possibleSupervisors);

        $form->populateValues($character);
        $form->setAttribute('action', '/castmanager/characters/edit/' . $id);

        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->characterTable->save($id, $form->getData());
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
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->familiesTable->remove($id);
            }

            // Redirect to list of Users
            return $this->redirect()->toRoute('castmanager/characters');
        }

        return array(
            'id' => $id,
            'family' => $this->familiesTable->getById($id)
        );
    }

    function jsonAction() {
        if (!$this->getRequest()->isXmlHttpRequest())
            return $this->notFoundAction();

        $request = json_decode($this->getRequest()->getContent());
        $result = ['error' => false];
        try {
            switch ($request->method) {
                case 'getPossibleSupervisors':
                    if (!isset($request->familyID) ) {
                        $result['data'] = false;
                    } else {
                        $result['data'] = $this->characterTable->getAllPossibleSupervisorsFor($request->familyID);
                    }
                    break;
                case 'getPossibleGuardians':
                    if (!isset($request->familyID) ) {
                        $result['data'] = false;
                    } else {
                        $result['data'] = $this->characterTable->getByFamilyId($request->familyID);
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

    function jsonOwnerEditAction() {
        if (!$this->getRequest()->isXmlHttpRequest())
            return $this->notFoundAction();

        $request = json_decode($this->getRequest()->getContent());
        $result = ['error' => false];
        try {
            switch ($request->method) {
                case 'saveChar':
                    if (!isset($request->id) || !isset($request->data) ) {
                        throw new Exception('id or data is not set');
                    } else {
                        $data = get_object_vars($request->data);
                        if ($request->id < 0) {
                            $data['active'] = 0;
                            $data['id'] = 0;
//                            $data['user_id'] =  $this->accessService->getUserID();

                            $form = $this->createCharacterForm();
                            $form->setValidationGroup(
                                'id'
                                ,'user_id'
                                ,'name'
                                ,'surename'
                                ,'gender'
                                ,'birthday'
//                                ,'guardian_id'
//                                ,'supervisor_id'
                                ,'vita'
                                ,'active'
//                                ,'family_id'
//                                ,'family_name'
//                                ,'blazon_id'
//                                ,'job_id'
//                                ,'job_name'
                            );
                            $form->setData($data);
                            if ($form->isValid() ){
                                unset($data['submit']);
                                $data['user_id'] = $this->accessService->getUserID();
                                $id = $this->characterTable->add($data);
                                $newOwn = $this->characterTable->getById($id);
                                $result['message'] = 'new char created';
                                $result['code'] = 201;
                                $result['data'] = $newOwn;
                            } else {
                                $result['error'] = true;
                                $result['message'] = 'form errors';
                                $result['code'] = 1;
                                $result['formErrors'] = $form->getMessages();
                            }
                        } else {
                            $charInDb = $this->characterTable->getById($request->id);
                            if (!$charInDb) {
                                $result['error'] = true;
                                $result['message'] = "Character id dose't exists";
                                $result['code'] = 2;
                            } else {
                                //check if current user is char owner
                                $charInDb = $charInDb[0];
                                if ( $this->accessService->getUserID() == $charInDb['user_id']) {
                                    $data['id'] = $request->id;
                                    $data['user_id'] = $this->accessService->getUserID();
                                    if ($this->characterTable->save($request->id, $data) ) {
                                        $result['message'] = 'Save Character';
                                        $result['data'] = $charInDb;
                                        $result['code'] = 200;
                                    } else {
                                        $result['error'] = true;
                                        $result['message'] = "Can't save Character";
                                        $result['code'] = 3;
                                    }
                                } else {
                                    $result['error'] = true;
                                    $result['message'] = "Forbidden Character for you";
                                    $result['code'] = 403;
                                }

                            }
                        }
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
        $families = $this->familiesTable->getAll();
        $users = $this->userService->getAllUsers();
        $jobs = $this->jobTable->getAll();
        $form = new CharacterForm($users->toArray(), $families, $jobs);
        return $form;
    }
}
