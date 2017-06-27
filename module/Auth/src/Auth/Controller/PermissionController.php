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
        $sortedRoles = [];
        $permsByRole = [];
        $notGiven = [];
        $given = [];
        $skip = true;

        $allPerms = $this->permTable->getResourcePermissions();
        $roleID = (int) $this->params('id');
        $sorted = $this->roleTable->fetchAllSorted();

        foreach ($sorted as $role)
            $sortedRoles[] = array(
                'id' => (int)$this->roleTable->getRoleIDByName($role),
                'role_name' => $role
            );

        foreach ($sortedRoles as $role){
            if ($role['id'] == $roleID)
                $skip = false;
            if ($skip) continue;
            $permsByRole[$role['role_name']] = $this->rolePermTable->getPermissionsByRoleID($role['id']);
        }
        
        $i=0;
        foreach ($permsByRole as $byRolename) {
            foreach ($byRolename as $item) {
                if (!$i == 0) $item['disabled'] = true;
                $item['id'] = $item['permission_id'];
                $given[$item['resource_name'] . ' - ' . $item['permission_name']] = $item;
            }
            $i++;
        }

        foreach ($allPerms as $key => $perm) {
            $needle = $perm['resource_name'] . ' - ' . $perm['permission_name'];
            if (!isset($given[$needle]))
                $notGiven[$perm['resource_name'] . ' - ' . $perm['permission_name']] = $perm;
        }
        
        ksort($given);
        ksort($notGiven);

        if(empty($notGiven)) $notGiven['Du bist der König der Welt!'] = array(
            'id' => 9999,
            'resource_name'   => 'Du bist',
            'permission_name' => 'der König der Welt!'
        );

        $addForm = new PermissionAddForm($notGiven);
        $addForm->get('role_id')->setValue($roleID);
        $addForm->get('submit')->setValue('Add');

        $deleteForm = new PermissionDeleteForm($given);
        $deleteForm->get('role_id')->setValue($roleID);
        $deleteForm->get('submit')->setValue('Delete');
        return array(
            'roleID' => $roleID,
            'roleName' => $this->roleTable->getRoleByID($roleID)['role_name'],
            'addForm' => $addForm,
            'deleteForm' => $deleteForm
        );
    }

    public function addAction() //ja die posts gehen nicht auf die selbe url die gehen nach add oder delete
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
//                $this->rolePermTable->delete("id = $id");
                $this->rolePermTable->deletePermission($post['role_id'], $id);
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