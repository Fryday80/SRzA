<?php
namespace Auth\Controller;

use Auth\Form\EmailForm;
use Auth\Utility\UserPassword;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\AbstractActionController;
use Auth\Form\LoginForm;
use Auth\Form\UserForm;
use Auth\Model\User;
use Application\Service\TemplateTypes;

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
        return $users;
    }

    public function loginAction()
    {
        $form = new LoginForm('Login');
        $request = $this->getRequest();
        $ip = $request->getServer('REMOTE_ADDR');
        $ref = $this->flashmessenger()->getMessagesFromNamespace('referer');
        if (count($ref) > 0) {
            $this->flashmessenger()->addMessage($ref[0], 'referer', 1);
        }
        if ($request->isPost()) {
            $form->setData($request->getPost());
       
            if ($form->isValid()) {
                $data = $form->getData();
                //check if user exists
                $userTable = $this->getServiceLocator()->get("Auth\Model\UserTable");
                $userDetails = $userTable->getUsersForAuth($data['email']);
                if ($userDetails->status == 'N') {
                    $this->flashmessenger()->addMessage("Zugang Verweigert::Dieser Account ist nicht Aktiviert", "MessagePage", 1);
                    return $this->redirect()->toRoute('message');
                }
                //check authentication... @todo move to AccessService
                $userPassword = new UserPassword();
                $encyptPass = $userPassword->create($data['password']);

                /** @var AuthenticationService $authService */
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
                    $storage = $this->getSessionStorage();
                    $storage->setUserID($userDetails->id);
                    $storage->setUserName($userDetails->name);
                    $storage->setRoleID($userDetails->role_id);
                    $storage->setRoleName($userDetails->role_name);
                    $storage->setIP($ip);
                    // check if it has rememberMe :
                    if ($request->getPost('rememberme') == 1) {
                        $storage->setRememberMe(1);
                    }
                    // set storage again
                    $authService->setStorage($storage);

                    if (count($ref) > 0) {
                        return $this->redirect()->toUrl($ref[0]);
                    }

                    $this->flashmessenger()->addMessage("You've been logged in");
                    return $this->redirect()->toUrl($this->getReferer());
                }
            }
        }
        return array(
            'form' => $form,
            'messages' => $this->flashmessenger()->getMessagesFromNamespace('auth')
        );
    }
    public function logoutAction()
    {
        $this->getSessionStorage()->forgetMe();
        $this->getServiceLocator()->get('AuthService')->clearIdentity();
        
        $this->flashmessenger()->addMessage("You've been logged out");
        return $this->redirect()->toUrl($this->getReferer());
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

    public function registerAction()
    {
        $form = new UserForm();
        $form->remove('status');
        $form->get('submit')->setValue('Registrieren');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $user = new User();
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $user->exchangeArray($form->getData());
                $user->status = "N";
                $userPassword = new UserPassword();
                $user->password = $userPassword->create($user->password);
                $userTable = $this->getServiceLocator()->get('Auth\Model\UserTable');
                $user->status = false;
                $userTable->saveUser($user);
                $msgService = $this->getServiceLocator()->get('MessageService');
                $msgService->SendMailFromTemplate(TemplateTypes::SUCCESSFUL_REGISTERED, $user);
                return $this->redirect()->toRoute('user');
            }
        }
        return array(
            'form' => $form
        );
    }
    //password reset action
    public function resetAction() {
        $form = new EmailForm();
        $form->get('submit')->setValue('Reset Password');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $email = $form->getData()['email'];
                $userTable = $this->getServiceLocator()->get('Auth\Model\UserTable');
                $user = $userTable->getUserByMail($email);
                if (!$user) {
                    $form->get('email')->setMessages(array('Email nicht gefunden.'));
                } else {
                    //create temp password
                    $userPassword = new UserPassword();
                    $tempPassword = $userPassword->generateRandom(8);
                    $user->password = $userPassword->create($tempPassword);
                    //send temp password
                    $msgService = $this->getServiceLocator()->get('MessageService');
                    if ($msgService->SendMailFromTemplate(TemplateTypes::RESET_PASSWORD, $user)) {
                        //if successful send
                        $userTable->saveUser($user);
                        //@todo add success page
                        return $this->redirect()->toRoute('home');
                    }
                    $this->flashmessenger()->addMessage("Server Error::Die nachricht konnte nicht gesendet werden. Bitte Sag dem admin bescheid", "MessagePage", 1);
                    return $this->redirect()->toRoute('message');
                }
            }
        }
        return array(
            'form' => $form,
            'messages' => $this->flashMessenger()->getMessagesFromNamespace("PasswordReset")
        );
    }

    //makeup reffering site to usable string
    protected function getReferer()
    {
        $base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/';
        $referringPage = str_replace($base_url, "", $_SERVER['HTTP_REFERER']);
        $referringPage = ($referringPage == "")? 'home' : $referringPage;
        return $referringPage;
    }
}
