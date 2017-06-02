<?php
namespace Auth\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Auth\Form\PermissionAddForm;
use Auth\Form\PermissionDeleteForm;

class PermissionController extends AbstractActionController
{

    public function indexAction()
    {
        $roleTable = $this->getServiceLocator()->get("Auth\Model\RoleTable");
        return array(
            'roles' => $roleTable->getUserRoles()
        );
    }

    public function editAction()
    {
        $roleID = (int) $this->params('id');
        $rolePermTable = $this->getServiceLocator()->get("Auth\Model\RolePermissionTable");
        $permTable = $this->getServiceLocator()->get("Auth\Model\PermissionTable");
        
        $perms = $rolePermTable->getPermissionsByRoleID($roleID);
        $allPerms = $permTable->getResourcePermissions();

        $addForm = new PermissionAddForm($allPerms);
        $addForm->get('role_id')->setValue($roleID);
        $addForm->get('submit')->setValue('Add');

        $deleteForm = new PermissionDeleteForm($perms);
        $deleteForm->get('role_id')->setValue($roleID);
        $deleteForm->get('submit')->setValue('Delete');
        return array(
            'roleID' => $roleID,
            'addForm' => $addForm,
            'deleteForm' => $deleteForm
        );
    }

    public function addAction()
    {
        $rolePermTable = $this->getServiceLocator()->get("Auth\Model\RolePermissionTable");
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            $roleID = (int) $post['role_id'];
            $permIDs = $post['permissions'];
            $rolePerms = $rolePermTable->getPermissionsByRoleID($roleID);
            

            
            // @todo salt check if the role have the perm allready
            foreach ($permIDs as $id) {
                $exists = false;
                foreach ($rolePerms as $perm) {
                    if ($id == $perm['permission_id']) {
                        $exists = true;
                    }
                }
                if ($exists)
                    continue;
                $rolePermTable->addPermission($roleID, $id);
            }
        }
        $this->cache->clearCache('acl');
        return $this->redirect()->toRoute('permission/edit', array(
            'id' => $roleID
        ));
    }

    public function deleteAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            $rolePermIDs = $post['role_permission_id'];
            $rolePermTable = $this->getServiceLocator()->get("Auth\Model\RolePermissionTable");
            foreach ($rolePermIDs as $id) {
                $rolePermTable->delete("id = $id");
            }
            $this->cache->clearCache('acl');
        }
        
        return $this->redirect()->toRoute('permission/edit', array(
            'id' => $post['role_id']
        ));
    }
}