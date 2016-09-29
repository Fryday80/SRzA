<?php
namespace Auth\Controller;

use Auth\Utility\UserPassword;
use Zend\Mvc\Controller\AbstractActionController;
use Auth\Form\LoginForm;
use Doctrine\DBAL\Schema\View;

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
                        'user_id' => 'id',
                        'email',
                        'user_name'
                    ));
        return $users[0];
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
        
                    $userDetails = $this->getUserDetails($data['email']);
                print('<br>');
                print('<pre>');
                var_dump($userDetails);
                print('</pre>');
                    $storage = $this->getSessionStorage();
                    $storage->setUserID($userDetails['user_id']);
                    $storage->setUserName($userDetails['user_name']);
                    $storage->setRoleID($userDetails['role_id']);
                    $storage->setRoleName($userDetails['role_name']);
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
        return $this->redirect()->toRoute('login');
    }

    public function accessDeniedAction()
    {}
}