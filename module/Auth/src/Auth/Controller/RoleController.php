<?php
namespace Auth\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Auth\Form\RoleForm;

class RoleController extends AbstractActionController
{
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
                $data = $form->getData();
                //if role_parent == 0 then set to null
                $roleTable->add($data['role_name'], $data['role_parent'], $data['status']);
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
        $id = $this->params('id');
        //@todo verify id
        $roleTable = $this->getServiceLocator()->get("Auth\Model\RoleTable");
        $form = new RoleForm($roleTable);
        $form->get('submit')->setValue('Edit');
        $data = $roleTable->getRoleByID($id)[0];
        $form->populateValues($data);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $roleTable->edit(array(
                    'role_name' => $data['role_name'],
                    'role_parent'=> $data['role_parent']
                ), $data['rid']);
                return $this->redirect()->toRoute('role');
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
        $roleTable = $this->getServiceLocator()->get("Auth\Model\RoleTable");
        $role = $roleTable->getRoleByID($id);
        
        if ($role == null) {
            //@todo add error: role with $id dosn't exists
            return $this->redirect()->toRoute('role');
        }
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
    
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $roleTable->deleteByID($id);
            }
           return $this->redirect()->toRoute('role');
        }
        return array(
            'id' => $id,
            'rolename' => $role['role_name']
        );
    }
}