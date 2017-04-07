<?php
namespace Auth\Controller;


use Application\Utility\DataTable;
use Auth\Service\AccessService;
use Auth\Utility\UserPassword;
use Auth\Form\UserForm;
use Auth\Model\User;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Permissions\Acl\Role\GenericRole;

class UserController extends AbstractActionController
{

    protected $userTable;

    public function __construct( ) {
        
    }
    public function indexAction()
    {
        $userTable = $this->getServiceLocator()->get("Auth\Model\UserTable");
        $data = $userTable->getUsers()->toArray();

        $userTable = new DataTable( array( 'data' => $data ));
        $userTable->insertLinkButton('/user/add', "Neuer Benutzer");
        $userTable->setColumns( array (
            array (
                'name'  => 'name',
                'label' => 'Name'
            ),
            array (
                'name'  => 'email',
                'label' => 'eMail'
            ),
            array (
                'name'  => 'role_name',
                'label' => 'Rolle'
            ),
            array (
                'name'  => 'href',
                'label' => 'Aktion',
                'type'  => 'custom',
                'render' => function ($row){
                    $edit = '<a href="user/edit/' . $row['id'] . '">Edit</a>';
                    $delete = '<a href="user/delete/' . $row['id'] . '">Delete</a>';
                    return $edit.' '.$delete;
                }
            ),
        ) );
        return array(
            'users' => $userTable
        );
    }

    public function addAction()
    {
        /**
         * @var $accessService AccessService
         */
        $accessService = $this->getServiceLocator()->get("AccessService");
        $userRole = $accessService->getRole();

        $roleTable = $this->getServiceLocator()->get("Auth\Model\RoleTable");
        $allRoles = $roleTable->getUserRoles();


        $form = new UserForm($allRoles, $userRole);
        $form->get('submit')->setValue('Add');
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $user = new User();
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $user->exchangeArray($form->getData());
                if (strlen($form->getData()['password']) > 4) {
                    $userPassword = new UserPassword();
                    $user->password = $userPassword->create($user->password);
                }
                $userTable = $this->getServiceLocator()->get("Auth\Model\UserTable");
                $userTable->saveUser($user);
                return $this->redirect()->toRoute('user');
            }
        }
        return array(
            'form' => $form
        );
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (! $id) {
            return $this->redirect()->toRoute('user');
        }

        $userTable = $this->getServiceLocator()->get("Auth\Model\UserTable");
        try {
            $user = $userTable->getUser($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('user');
        }

        $accessService = $this->getServiceLocator()->get("AccessService");
        $userRole = $accessService->getRole();

        $roleTable = $this->getServiceLocator()->get("Auth\Model\RoleTable");
        $allRoles = $roleTable->getUserRoles();

        $form = new UserForm($allRoles, $userRole);
        $form->setData($user->getArrayCopy());
        $form->get('submit')->setAttribute('value', 'Edit');
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $user->exchangeArray($form->getData());
                if (strlen($form->getData()['password']) > 3) {
                    $userPassword = new UserPassword();
                    $user->password = $userPassword->create($user->password);
                }
                $userTable->saveUser($user);
                
                // Redirect to list of Users
                return $this->redirect()->toRoute('user');
            }
        }

        return array(
            'id' => $id,
            'form' => $form
        );
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (! $id) {
            return $this->redirect()->toRoute('user');
        }
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
            
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $userTable = $this->getServiceLocator()->get("Auth\Model\UserTable");
                $userTable->deleteUser($id);
            }
            
            // Redirect to list of Users
            return $this->redirect()->toRoute('user');
        }
        $userTable = $this->getServiceLocator()->get("Auth\Model\UserTable");
        
        return array(
            'id' => $id,
            'user' => $userTable->getUser($id)
        );
    }
}
