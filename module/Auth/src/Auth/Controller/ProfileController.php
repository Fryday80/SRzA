<?php
namespace Auth\Controller;

use Auth\Form\ProfileCharacterForm;
use Auth\Model\UserTable;
use Auth\Service\AccessService;
use Cast\Model\CharacterTable;
use vakata\database\Exception;
use Zend\Mvc\Controller\AbstractActionController;
use Auth\Form\UserForm;
use Auth\Model\User;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class ProfileController extends AbstractActionController
{
    public function indexAction() {
        /** @var UserTable $userTable */
        $userTable = $this->getServiceLocator()->get('Auth/Model/UserTable');
        /** @var CharacterTable $characterTable */
        $characterTable = $this->getServiceLocator()->get('Cast/Model/CharacterTable');
        /** @var AccessService $accessService */
        $accessService = $this->getServiceLocator()->get('AccessService');
        $viewModel = new ViewModel();
        $username = $this->params()->fromRoute('username');
        $private = (!$username);
        //@todo handle guest if it's private (redirect)
        $username = ($username)? $username: $accessService->getUserName();
        /** @var User $user */
        $user = $userTable->getUsersWhere(array('name' => $username))->current();
        if (!$user) {
            throw Exception("todo");
            //@todo redirect to user list
        }
        $characters = $characterTable->getByUserId($user->id);
        bdump($user);
        bdump($characters);
        $viewModel->setVariable('user', $user);
        $viewModel->setVariable('characters', $characters);

        if ($private)
            $this->privateView($viewModel, $user, $userTable);
        else
            $this->publicView($viewModel, $user);

        return $viewModel;
    }
    function jsonAction() {
        /** @var CharacterTable $charTable */
        $charTable = $this->getServiceLocator()->get("Cast\Model\CharacterTable");
        $request = json_decode($this->getRequest()->getContent());
        $result = ['error' => false];
        /** @var AccessService $accessService */
        $accessService = $this->getServiceLocator()->get('AccessService');
        $userID = $accessService->getUserID();
        try {
            switch ($request->method) {
                case 'getChars':
                    if (!isset($request->userID) ) {
                        $result['data'] = $charTable->getByUserId($userID);
                    } else {
                        $result['data'] = $charTable->getByUserId($request->userID);
                    }
                    break;
                default:
                    $result['error'] = true;
                    $result['message'] = 'Method not found';
                    break;
            };
        } catch (Exception $e) {
            $result['error'] = true;
            $result['message'] = $e->getMessage();
        }

        //output
        return new JsonModel($result);
    }
    public function privateView(ViewModel &$viewModel, User $user, $userTable) {
        $viewModel->setTemplate('auth/profile/private.phtml');
        $request = $this->getRequest();

        //create userForm
        $form = new UserForm();
        $viewModel->setVariable('userForm', $form);

        $form->setData($user->getArrayCopy());
        $form->get('submit')->setAttribute('value', 'Edit');

        if ($request->isPost()) {
            //check witch form was sent
            if ($request->getPost('email') !== null) {
                //user form
                $form->setData($request->getPost());
                if ($form->isValid()) {
                    $user->exchangeArray($form->getData());
                    if (strlen($form->getData()['password']) > 3) {
                        $userPassword = new UserPassword();
                        $user->password = $userPassword->create($user->password);
                    }
                    $userTable->saveUser($user);

                }
            }
        }


        //create charForm
        $charForm = $this->createCharacterForm();
        $viewModel->setVariable('charForm', $charForm);


        //active / user_id

    }
    public function publicView(&$viewModel, User $user) {
        $viewModel->setTemplate('auth/profile/public.phtml');

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
        return new ProfileCharacterForm($users, $families, $jobs);
    }
}
