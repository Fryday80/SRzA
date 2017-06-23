<?php
namespace Auth\Controller;

use Application\Service\CacheService;
use Auth\Model\PermissionTable;
use Auth\Model\RolePermissionTable;
use Auth\Model\RoleTable;
use Zend\Mvc\Controller\AbstractActionController;
use Auth\Form\PermissionAddForm;
use Auth\Form\PermissionDeleteForm;

class PermissionController extends AbstractActionController
{
    /** @var RoleTable  */
    protected $roleTable;
    /** @var RolePermissionTable  */
    protected $rolePermTable;
    /** @var PermissionTable  */
    protected $permTable;
    /** @var CacheService */
    private $cacheService;
    
    function __construct(RoleTable $roleTable, RolePermissionTable $rolePermissionTable, PermissionTable $permissionTable, CacheService $cacheService)
    {
        $this->roleTable = $roleTable;
        $this->rolePermTable = $rolePermissionTable;
        $this->permTable = $permissionTable;
        $this->cacheService = $cacheService;
    }

    public function indexAction()
    {
        return array(
            'roles' => $this->roleTable->getUserRoles()
        );
    }

    public function editAction()
    {
        $perms = [];
        $pHash = [];
        $notGiven = [];
        $roleID = (int) $this->params('id');
        $sorted = $this->roleTable->fetchAllSorted();
        $sortedRoles = array();
        foreach ($sorted as $role)
            $sortedRoles[] = array(
                'id' => $this->roleTable->getRoleIDByName($role),
                'name' => $role
            );
        $skip = false;
        foreach ($sortedRoles as $role){
            if (!$skip)
                if ($role['id'] == $roleID)
                    $skip = true;
            $perms = $this->rolePermTable->getPermissionsByRoleID($role['id']);
        }
        $allPerms = $this->permTable->getResourcePermissions();
        foreach ($perms as $perm){
            $pHash[$perm['resource_name'] . '-' . $perm['permission_name']] = 1;
        }
        foreach ($allPerms as $perm) {
            if(isset($pHash[$perm['resource_name'] . '-' . $perm['permission_name']])) continue;
            else
                $notGiven[] = $perm;
        }
        $addForm = new PermissionAddForm($notGiven);
        $addForm->get('role_id')->setValue($roleID);
        $addForm->get('submit')->setValue('Add');

        $deleteForm = new PermissionDeleteForm($perms);
        $deleteForm->get('role_id')->setValue($roleID);
        $deleteForm->get('submit')->setValue('Delete');
        return array(
            'roleID' => $roleID,
            'roleName' => $this->roleTable->getRoleByID($roleID)['role_name'],
            'addForm' => $addForm,
            'deleteForm' => $deleteForm
        );
    }

    public function addAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            $roleID = (int)$post['role_id'];
            $permIDs = $post['permissions'];
            if (isset($permIDs)) {
                $rolePerms = $this->rolePermTable->getPermissionsByRoleID($roleID);
                foreach ($permIDs as $id) {
                    $exists = false;
                    foreach ($rolePerms as $perm) {
                        if ($id == $perm['permission_id']) {
                            $exists = true;
                        }
                    }
                    if ($exists)
                        continue;
                    $this->rolePermTable->addPermission($roleID, $id);
                }
                $this->clearCache();
            }
            return $this->redirect()->toRoute('permission/edit', array(
                'id' => $roleID
            ));
        }
    }

    public function deleteAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            $rolePermIDs = $post['role_permission_id'];
            foreach ($rolePermIDs as $id) {
                $this->rolePermTable->delete("id = $id");
            }
            $this->clearCache();
        }
        
        return $this->redirect()->toRoute('permission/edit', array(
            'id' => $post['role_id']
        ));
    }
    private function clearCache($type = 'acl'){
        $this->cacheService->clearCache($type);
    }
}