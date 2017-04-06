<?php
namespace Auth\Controller;


use Application\Utility\DataTable;
use Auth\Utility\UserPassword;
use Auth\Form\UserForm;
use Auth\Model\User;
use Zend\Mvc\Controller\AbstractActionController;

class UserController extends AbstractActionController
{

    protected $userTable;

    public function __construct( ) {
        
    }
    public function indexAction()
    {
        $data = $this->getUserTable()->getUsers()->toArray();
        bdump($data);
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
        $form = new UserForm();
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
                $this->getUserTable()->saveUser($user);
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
        
        // Get the users with the specified id. An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $user = $this->getUserTable()->getUser($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('user');
        }
        $form = new UserForm();
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
                $this->getUserTable()->saveUser($user);
                
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
                $this->getUserTable()->deleteUser($id);
            }
            
            // Redirect to list of Users
            return $this->redirect()->toRoute('user');
        }
        
        return array(
            'id' => $id,
            'user' => $this->getUserTable()->getUser($id)
        );
    }

    public function getUserTable()
    {
        if (! $this->userTable) {
            $sm = $this->getServiceLocator();
            $this->userTable = $sm->get('Auth\Model\UserTable');
        }
        return $this->userTable;
    }
}
