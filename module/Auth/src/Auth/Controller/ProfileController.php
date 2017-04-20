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
            $this->privateView($viewModel, $user);
        else
            $this->publicView($viewModel, $user);

        return $viewModel;
    }
    public function privateView(ViewModel &$viewModel, User $user) {
        $viewModel->setTemplate('auth/profile/private.phtml');

        //create userForm
        $form = new UserForm();
        $viewModel->setVariable('userForm', $form);

        //create charForm
        $familyTable = $this->getServiceLocator()->get("Cast\Model\FamiliesTable");
        $families = $familyTable->getAll();
        $charForm = new ProfileCharacterForm($families);
        $viewModel->setVariable('charForm', $charForm);

    }
    public function publicView(&$viewModel, User $user) {
        $viewModel->setTemplate('auth/profile/public.phtml');

    }
}
