<?php
namespace Auth\Controller;

use Application\Model\DynamicHashTable;
use Auth\Form\EmailForm;
use Auth\Model\UserTable;
use Auth\Utility\UserPassword;
use Exception;
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
                $valid = true;
                //check if user exists
                /** @var UserTable $userTable */
                $userTable = $this->getServiceLocator()->get("Auth\Model\UserTable");
                $userDetails = $userTable->getUsersForAuth($data['email']);
                // email not found
                if (!$userDetails){
                    $valid = false;
                }
                // email found but inactive
                elseif ($userDetails->status != 1) {
                    $valid = false;
                    $newMsg = "Zugang Verweigert::Dieser Account ist nicht Aktiviert";
                }
                if ($valid) {
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
                }
        
                if ($valid && $result->isValid()) {
                    $storage = $this->getSessionStorage();
                    $storage->setUserID($userDetails->id);
                    $storage->setUserName($userDetails->name);
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
                } else {
                    if (!isset ($newMsg)) {
                        $newMsg = 'Email oder Passwort falsch';
                    }
                    $msg = $form->get('password')->getMessages();
                    array_push($msg, $newMsg);
                    $form->get('password')->setMessages($msg);
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
    public function resetRequestAction() {
        $form = new EmailForm();
        $form->get('submit')->setValue('Reset Password');
        $isSend = false;
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $email = $form->getData()['email'];
                $userTable = $this->getServiceLocator()->get('Auth\Model\UserTable');
                /** @var User $user */
                $user = $userTable->getUserByMail($email);
                if (!$user) {
                    $form->get('email')->setMessages(array('Email nicht gefunden.'));
                } else {
//                    send temp password
                    /** @var DynamicHashTable $dynamicHashTable */
                    $dynamicHashTable = $this->getServiceLocator()->get('Application\Model\DynamicHashTable');
                    $hash = $dynamicHashTable->create(600);//@todo get value from config
                    $msgService = $this->getServiceLocator()->get('MessageService');
                    $templateData = [
                        'userName' => $user->name,
                        'userEmail' => $user->email,
                        'hash' => $hash,
                    ];
                    if ($msgService->SendMailFromTemplate($user->email, TemplateTypes::RESET_PASSWORD, $templateData)) {
                        //@todo redirect to email provider
                        $isSend = true;
//                        return $this->redirect()->toRoute('home');
//$this->flashMessenger()->getMessagesFromNamespace("PasswordReset"),

                    } else {
                        throw new Exception('Die nachricht konnte nicht gesendet werden.');
                    }
                }
            }
        }
        return array(
            'form' => $form,
            'isSend' => $isSend
        );
    }

    public function resetAction() {
        /** @var DynamicHashTable $dynamicHashTable */
        $dynamicHashTable = $this->getServiceLocator()->get('Application\Model\DynamicHashTable');

        $request = $this->getRequest();
        $hash = $this->params('hash');

        $savedHash = $dynamicHashTable->getByHash($hash);
        if (!$savedHash) {
            //no such a hash
            return array(
                'message' => 'hash nicht gefunden. eventuel ist der hash schon zu alt.',
                'hashError' => true
            );
        }

        //hash accepted
        //render form for new pass
        $form = new UserForm();

        return array(
            'pwForm' => $form,
            'hashError' => false
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
