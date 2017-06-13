<?php
namespace Auth\Controller;

use Application\Service\StatisticService;
use Auth\Form\ProfileCharacterForm;
use Auth\Service\AccessService;
use Auth\Service\UserService;
use Auth\Utility\UserPassword;
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
    /** @var FamiliesTable  */
    protected $familyTable;
    /** @var JobTable  */
    protected $jobTable;
    /** @var StatisticService  */
    protected $statsService;
    /** @var CastService  */
    protected $castService;
    /** @var UserService  */
    protected $userService;
    /** @var AccessService  */
    private $accessService;

    function __construct(
        FamiliesTable $familyTable,
        JobTable $jobTable,
        StatisticService $statService,
        CastService $castService,
        AccessService $accessService,
        UserService $userService
    )
    {
        $this->familyTable = $familyTable;
        $this->jobTable = $jobTable;
        $this->statsService = $statService;
        $this->castService = $castService;
        $this->accessService = $accessService;
        $this->userService = $userService;
    }

    public function indexAction() {
        return $this->redirect()->toRoute('myprofile');
    }
    function jsonAction() {
        $request = json_decode($this->getRequest()->getContent());
        $result = ['error' => false];
        $userID =  $this->accessService->getUserID();
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

    public function publicProfileAction()
    {
        $viewModel = new ViewModel();
        $username = $this->params()->fromRoute('username');
        /** @var User $user */
        $user = $this->userService->getUserDataBy('name', $username);
        if (!$user) {
//            throw Exception("todo");
            //@todo redirect to user list
            $this->redirect()->toRoute('home');
        }

        $characters = $this->castService->getByUserId($user->id);
        $isActive = $this->statsService->isActive($user->name);
        $askingUser = $this->accessService->getUserName();

        $viewModel->setVariable('askingUser', $askingUser);
        $viewModel->setVariable('isActive', $isActive);
        $viewModel->setVariable('user', $user);
        $viewModel->setVariable('characters', $characters);

        $viewModel->setTemplate('auth/profile/public.phtml');

        return $viewModel;

    }
    public function privateProfileAction() {

        $username = $this->accessService->getUserName();
        if(!$username) return $this->redirect()->toRoute('home');
        /** @var User $user */
        $user = $this->userService->getUserDataBy('name', $username);

        $characters = $this->castService->getByUserId($user->id);
        $isActive = $this->statsService->isActive($user->name);

        $viewModel = new ViewModel();
        $viewModel->setTemplate('auth/profile/private.phtml');
        $viewModel->setVariable('askingUser', $username);
        $viewModel->setVariable('isActive', $isActive);
        $viewModel->setVariable('user', $user);
        $viewModel->setVariable('characters', $characters);

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
                    $id =  $this->accessService->getUserID();
                    if ($id === 0) return;
                    if ($id !== $form->get('id')->getValue()) return;
                    $form->get('id')->setValue($id);
                    $user->exchangeArray($form->getData());
                    if (strlen($form->getData()['password']) > MIN_PW_LENGTH) {
                        $userPassword = new UserPassword();
                        $user->password = $userPassword->create($user->password);
                    }
                    $this->userService->saveUser($user);

                }
            }
        }
        //create charForm
        $charForm = $this->createCharacterForm();
        $charForm->setAttribute('action', '#');
        $viewModel->setVariable('charForm', $charForm);
        return $viewModel;
    }

    private function createCharacterForm() {
        $families = $this->familyTable->getAll();
        $users = $this->userService->getAllUsers()->toArray();
        $jobs = $this->jobTable->getAll();
        return new ProfileCharacterForm($users, $families, $jobs);
    }
    public function charprofileAction(){
        $hasFamily = false;
        $username = $this->params()->fromRoute('username');
        $charnameURL = $this->params()->fromRoute('charname');

        $char = $this->castService->getCharacterData($charnameURL, $username);
        $char['userData'] = $this->userService->getUserDataBy('name', $username);
        $charFamily = $this->castService->getAllCharsFromFamily($char['family_id']);
        
        foreach ($charFamily as $key => $member){
            $wholeName = str_replace(" ", "-", $member['name']) . "-" . str_replace(" ", "-", $member['surename']);
            if ($wholeName == $charnameURL){
                unset($charFamily[$key]);
                break;
            }
        }
        
        if ($charFamily) $hasFamily = true;

        return  new ViewModel(array(
            'char'       => $char,
            'hasFamily'  => $hasFamily,
            'charFamily' => $charFamily,
            'username'   => $username,
            'charname'   => $charnameURL,
        ));


    }
    //@todo familyprofileAction
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
