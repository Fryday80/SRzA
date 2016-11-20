<?php
namespace Auth\Controller;

use Auth\Utility\UserPassword;
use Zend\Mvc\Controller\AbstractActionController;
use Auth\Form\LoginForm;

class AuthController extends AbstractActionController
{

    protected $form;

    protected $storage;

    private function getSessionStorage()
    {
        if (! $this->storage) {
            $this->storage = $this->getServiceLocator()->get('Auth\Model\AuthStorage');
        }
        
        return $this->storage;
    }

    private function getUserDetails($mail)
    {
        $userTable = $this->getServiceLocator()->get("Auth\Model\UserTable");
        $users = $userTable->getUsers(array(
                        'email' => $mail
                    ), array(
                        'id' => 'id',
                        'email',
                        'name'
                    ));
        var_dump($users[0]);
        return $users;
    }

    public function loginAction()
    {
        $form = new LoginForm('Login');
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            $form->setData($request->getPost());
       
            if ($form->isValid()) {
                $data = $form->getData();
        
                // check authentication...
                $userPassword = new UserPassword();
                $encyptPass = $userPassword->create($data['password']);
        
                $authService = $this->getServiceLocator()->get('AuthService');
        
                $authService->getAdapter()
                ->setIdentity($data['email'])
                ->setCredential($encyptPass);
        
                $result = $authService->authenticate();

                foreach ($result->getMessages() as $message) {
                    // save message temporary into flashmessenger
                    $this->flashmessenger()->addMessage($message);
                }
        
                if ($result->isValid()) {

                    $userTable = $this->getServiceLocator()->get("Auth\Model\UserTable");
                    $userDetails = $userTable->getUsersForAuth($data['email']);
                    $storage = $this->getSessionStorage();
                    $storage->setUserID($userDetails->id);
                    $storage->setUserName($userDetails->name);
                    $storage->setRoleID($userDetails->role_id);
                    $storage->setRoleName($userDetails->role_name);
                    // check if it has rememberMe :
                    if ($request->getPost('rememberme') == 1) {
                        $storage->setRememberMe(1);
                    }
                    // set storage again
                    $authService->setStorage($storage);
                    return $this->redirect()->toRoute('success');
                }
            }
        }
        return array(
            'form' => $form,
            'messages' => $this->flashmessenger()->getMessages()
        );
    }
    public function logoutAction()
    {
        $this->getSessionStorage()->forgetMe();
        $this->getServiceLocator()->get('AuthService')->clearIdentity();
        
        $this->flashmessenger()->addMessage("You've been logged out");
        return $this->redirect()->toRoute('home');
    }

    public function successAction()
    {
        if (! $this->getServiceLocator()->get('AuthService')->hasIdentity()){
            return $this->redirect()->toRoute('login');
        }
        return array(
        );
    }
    public function accessDeniedAction()
    {}
}
