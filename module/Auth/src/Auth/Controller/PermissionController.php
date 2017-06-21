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
        $pHash = [];
        $notGiven = [];
        $roleID = (int) $this->params('id');
        $perms = $this->rolePermTable->getPermissionsByRoleID($roleID);
        $allPerms = $this->permTable->getResourcePermissions();
        foreach ($perms as $perm){
            $pHash[$perm['resource_name'] . '-' . $perm['permission_name']] = 1;
        }
        foreach ($allPerms as $perm) {
            if(isset($pHash[$perm['resource_name'] . '-' . $perm['permission_name']])) continue;
            else
                $notGiven[] = $perm;
        }
        bdump($allPerms);
//        $addForm = new PermissionAddForm($allPerms);
        $addForm = new PermissionAddForm($notGiven);
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
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            $roleID = (int)$post['role_id'];
            $permIDs = $post['permissions'];
            if (isset($permIDs)) {
                $rolePerms = $this->rolePermTable->getPermissionsByRoleID($roleID);


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