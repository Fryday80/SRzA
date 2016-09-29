<?php
namespace Auth\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Auth\Form\RoleForm;

class RoleController extends AbstractActionController
{

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

    public function indexAction()
    {
        //list all roles
        $roleTable = $this->getServiceLocator()->get("Auth\Model\RoleTable");
        return array(
            'roles' => $roleTable->getUserRoles(),
        );
    }
    public function addAction()
    {

        $roleTable = $this->getServiceLocator()->get("Auth\Model\RoleTable");
        $form = new RoleForm($roleTable);
        

        
        $form->get('submit')->setValue('Add');
        
        $request = $this->getRequest();
        if ($request->isPost()) {

            $form->setData($request->getPost());
            if ($form->isValid()) {
//   save data to 
//                 $user->exchangeArray($form->getData());
//                 $this->getUserTable()->saveUser($user);
                
                // Redirect to list of Users
                return $this->redirect()->toRoute('role');
            } else {
                
            }
        }
        return array(
            'form' => $form
        );
    }
    public function editAction()
    {
        //list all roles
        return array(
            'form' => 42,
        );
    }
    public function deleteAction()
    {
        //list all roles
        return array(
            'form' => 42,
        );
    }
}