<?php
namespace Auth\Controller;

use Application\Service\StatisticService;
use Auth\Form\ProfileCharacterForm;
use Auth\Model\UserTable;
use Auth\Service\AccessService;
use Cast\Model\FamiliesTable;
use Cast\Model\JobTable;
use Cast\Service\CastService;
use vakata\database\Exception;
use Zend\Mvc\Controller\AbstractActionController;
use Auth\Form\UserForm;
use Auth\Model\User;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class ProfileController extends AbstractActionController
{
    /** @var UserTable  */
    protected $userTable;
    /** @var FamiliesTable  */
    protected $familyTable;
    /** @var JobTable  */
    protected $jobTable;
    /** @var AccessService  */
    protected $accessService;
    /** @var StatisticService  */
    protected $statsService;
    /** @var CastService  */
    protected $castService;

    function __construct(UserTable $userTable, FamiliesTable $familyTable, JobTable $jobTable, AccessService $accessService, StatisticService $statService, CastService $castService)
    {
        $this->userTable = $userTable;
        $this->familyTable = $familyTable;
        $this->jobTable = $jobTable;
        $this->accessService = $accessService;
        $this->statsService = $statService;
        $this->castService = $castService;
    }

    public function indexAction() {
        $viewModel = new ViewModel();
        $username = $this->params()->fromRoute('username');
        $private = (!$username);
        //@todo handle guest if it's private (redirect)
        $username = ($username)? $username: $this->accessService->getUserName();
        /** @var User $user */
        $user = $this->userTable->getUsersWhere(array('name' => $username))->current();
        if (!$user) {
            throw Exception("todo");
            //@todo redirect to user list
        }
        
        $characters = $this->castService->getByUserId($user->id);
        $isActive = $this->statsService->isActive($user->name);
        $askingUser = $this->accessService->getUserName();

        //cleanfix
//bdump($user);
//        bdump($characters);
        $viewModel->setVariable('askingUser', $askingUser);
        $viewModel->setVariable('isActive', $isActive);
        $viewModel->setVariable('user', $user);
        $viewModel->setVariable('characters', $characters);

        if ($private)
            $this->privateView($viewModel, $user, $this->userTable, $this->accessService);
        else
            $this->publicView($viewModel, $user);

        return $viewModel;
    }
    function jsonAction() {
        $request = json_decode($this->getRequest()->getContent());
        $result = ['error' => false];
        $userID = $this->accessService->getUserID();
        try {
            switch ($request->method) {
                case 'getChars':
                    if (!isset($request->userID) ) {
                        $result['data'] = $this->castService->getByUserId($userID);
                    } else {
                        $result['data'] = $this->castService->getByUserId($request->userID);
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
    public function privateView(ViewModel &$viewModel, User $user) {
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
                    $this->userTable->saveUser($user);

                }
            }
        }
        //create charForm
        $charForm = $this->createCharacterForm();
        $charForm->setAttribute('action', '#');
        $viewModel->setVariable('charForm', $charForm);
    }
    public function publicView(&$viewModel, User $user) {
        $viewModel->setTemplate('auth/profile/public.phtml');

    }

    private function createCharacterForm() {
        $families = $this->familyTable->getAll();
        $users = $this->userTable->getUsers()->toArray();
        $jobs = $this->jobTable->getAll();
        return new ProfileCharacterForm($users, $families, $jobs);
    }
    public function charprofileAction(){
        $hasFamily = false;
        $username = $this->params()->fromRoute('username');
        $charnameURL = $this->params()->fromRoute('charname');

        $char = $this->castService->getCharacterData($charnameURL, $username);
        $char['userData'] = $this->userTable->getUsersBy('name', $username);
        $charFamily = $this->castService->getAllCharsFromFamily($char['family_id']);
        
        foreach ($charFamily as $key => $member){
            $wholeName = str_replace(" ", "-", $member['name']) . "-" . str_replace(" ", "-", $member['surename']);
            if ($wholeName == $charnameURL){
                unset($charFamily[$key]);
                break;
            }
        }
        
        if ($charFamily) $hasFamily = true;

        return new ViewModel(array(
            'char'       => $char,
            'hasFamily'  => $hasFamily,
            'charFamily' => $charFamily,
            'username'   => $username,
            'charname'   => $charnameURL,
        ));

    }
//    public function familyprofileAction(){
//        $username = $this->params()->fromRoute('username');
//        $charname = $this->params()->fromRoute('charname');
//        /** @var  CastService $castService */
//        $castService = $this->getServiceLocator()->get("CastService");
//        $char = $castService->getCharacterData($charname, $username);
//        $charFamily = $castService->getAllCharsFromFamily($char['family_id']);
//
//
//        return new ViewModel(array(
//            'char'       => $char,
//            'charFamily' => $charFamily,
//            'username'   => $username,
//            'charname'   => $charname,
//        ));
//
//    }
}
