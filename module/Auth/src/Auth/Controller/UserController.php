<?php
namespace Auth\Controller;

use Application\Utility\DataTable;
use Auth\Model\RoleTable;
use Auth\Model\UserTable;
use Auth\Service\AccessService;
use Auth\Utility\UserPassword;
use Auth\Form\UserForm;
use Auth\Model\User;
use Zend\Mvc\Controller\AbstractActionController;

class UserController extends AbstractActionController
{
    /** @var  AccessService */
    protected $accessService;
    /** @var UserTable */
    protected $userTable;
    /** @var  RoleTable */
    protected $roleTable;

    public function __construct(UserTable $userTable, AccessService $accessService, RoleTable $roleTable)
    {
        $this->accessService = $accessService;
        $this->userTable = $userTable;
        $this->roleTable = $roleTable;
    }
    public function indexAction()
    {
        $data = $this->userTable->getUsers()->toArray();

        $userDataTable = new DataTable( array( 'data' => $data ));
        $userDataTable->insertLinkButton('/user/add', "Neuer Benutzer");
        $userDataTable->setColumns( array (
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
            'users' => $userDataTable
        );
    }

    public function addAction()
    {
        $userRole = $this->accessService->getRole();

        $allRoles = $this->roleTable->getUserRoles();


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
                $this->userTable->saveUser($user);
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
        
        try {
            $user = $this->userTable->getUser($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('user');
        }

        $userRole = $this->accessService->getRole();

        $allRoles = $this->roleTable->getUserRoles();

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
                $this->userTable->saveUser($user);
                
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
                $this->userTable->deleteUser($id);
            }
            
            // Redirect to list of Users
            return $this->redirect()->toRoute('user');
        }
        
        return array(
            'id' => $id,
            'user' => $this->userTable->getUser($id)
        );
    }
}
