<?php
namespace Auth\Controller;

use Application\Service\CacheService;
use Auth\Model\RoleTable;
use Zend\Mvc\Controller\AbstractActionController;
use Auth\Form\RoleForm;

class RoleController extends AbstractActionController
{
    /** @var RoleTable  */
    protected $roleTable;
    /** @var CacheService */
    protected $cacheService = false;
    
    function __construct(RoleTable $roleTable, CacheService $cacheService)
    {
        $this->roleTable = $roleTable;
        $this->cacheService = $cacheService;
    }

    public function indexAction()
    {
        //list all roles
        return array(
            'roles' => $this->roleTable->getUserRoles(),
        );
    }
    public function addAction()
    {
        $form = new RoleForm($this->roleTable);
        $form->get('submit')->setValue('Add');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                //if role_parent == 0 then set to null
                $this->roleTable->add($data['role_name'], $data['role_parent']);
                $this->clearCache();
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
        $form = new RoleForm($this->roleTable);
        $form->get('submit')->setValue('Edit');
        $data = $this->roleTable->getRoleByID($id);
        $form->populateValues($data);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $this->roleTable->edit(array(
                    'role_name' => $data['role_name'],
                    'role_parent'=> $data['role_parent']
                ), $data['rid']);
                $this->clearCache();
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
        $role = $this->roleTable->getRoleByID($id);
        
        if ($role == null) {
            //@todo add error: role with $id dosn't exists
            return $this->redirect()->toRoute('role');
        }
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
    
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->roleTable->deleteByID($id);
                $this->clearCache();
            }
            //cleanfix @todo remove
//            $this->navService->removeRole($id);
           return $this->redirect()->toRoute('role');
        }
        return array(
            'id' => $id,
            'rolename' => $role['role_name']
        );
    }
    private function clearCache($type = 'acl'){
        $this->cacheService->clearCache($type);
    }
}
