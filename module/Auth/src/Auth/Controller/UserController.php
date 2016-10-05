<?php
namespace Auth\Controller;


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
//         if (!$this->getAuthService()->hasIdentity()) {
//             print("access denied");die;
//         }
        return array(
            'users' => $this->getUserTable()->select()->toArray()
        );
    }

    public function addAction()
    {
        $form = new UserForm();
        $form->get('submit')->setValue('Add');
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getUserTable()->saveUser($form->getData());
                
                // Redirect to list of Users
                return $this->redirect()->toRoute('user');
            } else {
                
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
            return $this->redirect()->toRoute('user', array(
                'action' => 'add'
            ));
        }
        
        // Get the users with the specified id. An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $user = $this->getUserTable()->getUser($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('user', array(
                'action' => 'index'
            ));
        }
        $form = new UserForm();
        $form->setData($user);
        $form->get('submit')->setAttribute('value', 'Edit');
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            //$form->setInputFilter($user->getInputFilter());
            $form->setData($request->getPost());
            
            if ($form->isValid()) {
                //show errors
                
                print('not implemented');
                die;
                
                
                //$this->getUserTable()->saveUser($user);
                
                // Redirect to list of Users
                //return $this->redirect()->toRoute('user');
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