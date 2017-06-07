<?php
namespace Auth\Controller;

use Application\Model\DynamicHashTable;
use Application\Service\MessageService;
use Auth\Form\EmailForm;
use Auth\Form\PWForgetForm;
use Auth\Model\AuthStorage;
use Auth\Model\UserTable;
use Auth\Utility\UserPassword;
use Exception;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Result;
use Zend\Mvc\Controller\AbstractActionController;
use Auth\Form\LoginForm;
use Auth\Form\UserForm;
use Auth\Model\User;
use Application\Service\TemplateTypes;

class AuthController extends AbstractActionController
{
    protected $form;

    /** @var AuthStorage */
    protected $storage;
    /** @var AuthenticationService  */
    protected $authService;
    /** @var UserTable  */
    protected $userTable;
    /** @var DynamicHashTable  */
    protected $dynamicHashTable;
    /** @var  MessageService */
    protected $msgService;

    function __construct(AuthStorage $storage, AuthenticationService $authService, UserTable $userTable, DynamicHashTable $dynamicHashTable, MessageService $msgService )
    {
        $this->storage = $storage;
        $this->authService = $authService;
        $this->userTable = $userTable;
        $this->dynamicHashTable = $dynamicHashTable;
        $this->msgService = $msgService;
    }

    private function getUserDetails($mail)
    {
        $users = $this->userTable->getUsers(array(
                        'email' => $mail
                    ), array(
                        'id' => 'id',
                        'email',
                        'name'
                    ));
        return $users;
    }

    private function login($ip, $email, $clearPass) {
        $valid = true;
        $result = new Result(500, null, ['Unexpected Server Error']);
        //check if user exists
        $userDetails = $this->userTable->getUsersForAuth($email);
        // email not found
        if (!$userDetails){
            $valid = false;
        }
        // email found but inactive
        elseif ($userDetails->status != 1) {
            $valid = false;
            $result = new Result(401, null, ['Zugang Verweigert::Dieser Account ist nicht Aktiviert']);
        }
        if ($valid) {
            $userPassword = new UserPassword();
            $encyptPass = $userPassword->create($clearPass);

            $this->authService->getAdapter()
                ->setIdentity($email)
                ->setCredential($encyptPass);

            $result = $this->authService->authenticate();
        }

        if ($valid && $result->isValid()) {
            $this->storage->setUserID($userDetails->id);
            $this->storage->setUserName($userDetails->name);
            $this->storage->setRoleName($userDetails->role_name);
            $this->storage->setIP($ip);
            // check if it has rememberMe : //@todo reimplement
//            if ($request->getPost('rememberme') == 1) {
//                $this->storage->setRememberMe(1);
//            }
            // set storage again
            $this->authService->setStorage($this->storage);
        }
        return $result;
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

                $result = $this->login($ip, $data['email'], $data['password']);

                if ($result->isValid()) {
                    if (count($ref) > 0) {
                        return $this->redirect()->toUrl($ref[0]);
                    }
                    $this->flashmessenger()->addMessage("You've been logged in");
                    return $this->redirect()->toUrl($this->getReferer());
                } else {
                    foreach ($result->getMessages() as $message) {
                        // save message temporary into flashmessenger
                        $this->flashmessenger()->addMessage($message);
                    }
                    if (true) {//count($result->getMessages()) == 0) {
                        $form->get('email')->setMessages(['Email oder Passwort falsch']);
                        $form->get('password')->setMessages(['Email oder Passwort falsch']);
                    } else {
                        $form->get('password')->setMessages($result->getMessages());
                    }
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
        $this->storage->forgetMe();
        $this->authService->clearIdentity();
        
        $this->flashmessenger()->addMessage("You've been logged out");
        return $this->redirect()->toUrl($this->getReferer());
    }

    public function successAction()
    {
        if (! $this->authService->hasIdentity()){
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
                $user->status = false;
                $this->userTable->saveUser($user);
                $this->msgService->SendMailFromTemplate($user->email, TemplateTypes::SUCCESSFUL_REGISTERED, $user->getArrayCopy());
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
                /** @var User $user */
                $user = $this->userTable->getUserByMail($email);
                if (!$user) {
                    $form->get('email')->setMessages(array('Email nicht gefunden.'));
                } else {
//                    send temp password
                    $hash = $this->dynamicHashTable->create($email, 600);//@todo get value from config
                    $templateData = [
                        'userName' => $user->name,
                        'userEmail' => $user->email,
                        'hash' => $hash,
                    ];
                    if ($this->msgService->SendMailFromTemplate($user->email, TemplateTypes::RESET_PASSWORD, $templateData)) {
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
        //render form for new pass
        $form = new PWForgetForm();
        $request = $this->getRequest();
        $hash = $this->params('hash');
        $savedHash = $this->dynamicHashTable->getByHash($hash);
        //hash accepted?
        if (!$savedHash) {
            //no such a hash
            return array(
                'message' => 'Hash nicht gefunden. Eventuell ist der hash schon zu alt.',
                'error' => true
            );
        }

        if ($request->isPost())
        {
            $form->setData($request->getPost());
            if($form->isValid()){
                $this->changePW($savedHash['email'], $request->getPost('password'));
                //clean hash
                $this->dynamicHashTable->deleteByHash($hash);
            } else {
                return array(
                    'pwForm' => $form,
                    'message' => 'Die beiden eingegebenen Passwörter müssen gleich sein!',
                    'error' => true
                );
            }
        }

        return array(
            'pwForm' => $form,
            'error' => false,
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
    protected function changePW($email, $newPW){
        $userPassword = new UserPassword();
        $encyptPass = $userPassword->create($newPW);
        /** @var User $user */
        $user = $this->userTable->getUserByMail($email);
        $user->password = $encyptPass;
        $this->userTable->saveUser($user);
    }
}
