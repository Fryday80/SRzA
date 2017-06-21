<?php
namespace Cast\Controller;

use Auth\Service\AccessService;
use Cast\Form\CharacterForm;
use Cast\Service\CastService;
use Cast\Utility\CharacterDataTable;
use Exception;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class CharacterController extends AbstractActionController
{
    /** @var AccessService  */
    private $accessService;
    
    /** @var CastService  */
    private $castService;

    public function __construct( AccessService $accessService, CastService $castService )
    {
        $this->accessService = $accessService;
        $this->castService = $castService;
    }

    public function indexAction() {
        $characters = $this->castService->getAllChars();
        $charTable = new CharacterDataTable();
        $charTable->setData($characters);
        $charTable->setButtons('all');
        $charTable->insertLinkButton('/castmanager/characters/add', 'neuer Charakter');
        $charTable->insertLinkButton('/castmanager', 'Zurück');
        return new ViewModel(array(
            'families' => $charTable,
        ));
    }

    public function addAction() {
        $form = new CharacterForm($this->castService);
        $form->isBackend();
        $form->get('submit')->setValue('add');
        $form->setAttribute('action', '/castmanager/characters/add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $this->castService->addChar($data);
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
        if ( !$character = $this->castService->getCharById( $id ) ) {
            return $this->redirect()->toRoute('castmanager/characters');
        }
        $form = new CharacterForm($this->castService);
        $form->isBackend();
        $operator = 'Edit';
        $form->get('submit')->setAttribute('value', $operator);

        $possibleGuardians = $this->castService->getCharByFamilyId ( $character['family_id']);
        $possibleSupervisors = $this->castService->getAllPossibleSupervisorsFor($character['tross_id']);

        $form->setPossibleGuardians($possibleGuardians);
        $form->setPossibleSupervisors($possibleSupervisors);


//        $form-get('su')


        $form->populateValues($character);
        $form->setAttribute('action', '/castmanager/characters/edit/' . $id);

        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->castService->saveChar($id, $form->getData());
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
                $this->castService->deleteCharById($id);
            }

            // Redirect to list of Users
            return $this->redirect()->toRoute('castmanager/characters');
        }

        return array(
            'id' => $id,
            'char' => $this->castService->getCharById($id),
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
                        $result['data'] = $this->castService->getAllPossibleSupervisorsFor($request->familyID);
                    }
                    break;
                case 'getPossibleGuardians':
                    if (!isset($request->familyID) ) {
                        $result['data'] = false;
                    } else {
                        $result['data'] = $this->castService->getCharByFamilyId( $request->familyID);
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
                        if ($request->id <= 0) {
                            $data['active'] = 0;
                            $data['id'] = 0;
//                            $data['user_id'] =  $this->accessService->getUserID();

                            $form = new CharacterForm($this->castService);
                            $form->isBackend();
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
//                                ,'active'
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
                                $id = $this->castService->addChar($data);
                                $newOwn = $this->castService->getCharById($id);
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
                            $form = new CharacterForm($this->castService);
                            $form->isBackend();
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
//                                ,'active'
//                                ,'family_id'
//                                ,'family_name'
//                                ,'blazon_id'
//                                ,'job_id'
//                                ,'job_name'
                            );
                            $form->setData($data);
                            if ($form->isValid() ){
                                $charInDb = $this->castService->getCharById( $request->id );
                                if (!$charInDb) {
                                    $result['error'] = true;
                                    $result['message'] = "Character id dose't exists";
                                    $result['code'] = 2;
                                } else {
                                    //check if current user is char owner
                                    if ( $this->accessService->getUserID() == $charInDb['user_id']) {
                                        $data['id'] = $request->id;
                                        $data['user_id'] = $this->accessService->getUserID();
                                        if ($this->castService->saveChar($request->id, $data) ) {
                                            $result['message'] = 'Save Character';
                                            $result['data'] = $data;
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
                            } else {
                                $result['error'] = true;
                                $result['message'] = 'form errors';
                                $result['code'] = 1;
                                $result['formErrors'] = $form->getMessages();
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
}
