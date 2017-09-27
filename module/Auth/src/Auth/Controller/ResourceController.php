<?php
namespace Auth\Controller;

use Application\Service\CacheService;
use Auth\Model\Tables\PermissionTable;
use Auth\Model\Tables\ResourceTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Auth\Form\ResourceForm;

class ResourceController extends AbstractActionController
{
    /** @var ResourceTable  */
    protected $resTable;
    /** @var PermissionTable  */
    protected $permTable;
    /** @var CacheService */
    protected $cacheService;
    
    function __construct(ResourceTable $resTable, PermissionTable $permTable, CacheService $cacheService)
    {
        $this->resTable = $resTable;
        $this->permTable = $permTable;
        $this->cacheService = $cacheService;
    }

    public function indexAction()
    {
        return array(
            'resources' => $this->resTable->getAllResources()
        );
    }
    public function addAction() {
        $form = new ResourceForm();
        $form->get('submit')->setValue('Add');
        
        $request = $this->getRequest();
        if ($request->isPost()) {

            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $perms = explode( ',', str_replace(" ", "", $data['permissions']) );
                $name = $data['resource_name'];
                //create new resource
                $resID = $this->resTable->add($name);
                //create new permissions
                foreach ($perms as $value) {
                    $this->permTable->add($resID, $value);
                }
                $this->clearCache();
                return $this->redirect()->toRoute('resource');
            } else {
                
            }
        }
        return array(
            'form' => $form
        );
    }
    public function editAction() {
        $form = new ResourceForm();
        $form->get('submit')->setValue('Edit');
        $id = $this->params('id');
        if ($id == null) {
            //error -> need id
            return $this->redirect()->toRoute('resource');
        }
        //find resource with id
        $resource = $this->resTable->getByID($id);
        //@todo error if resource with id dosen't exists
        $perms = $this->permTable->getByResourceID($id);
        $permString = '';
        $permNames = array();
        foreach ($perms as $perm) {
            $permString = $permString . $perm['permission_name'] . ',';
            array_push($permNames, $perm['permission_name']);
        }
        $permString = trim($permString, ",");
        $formData = [
            'resource_name' => $resource['resource_name'],
            'permissions' => $permString
        ];
        $form->populateValues($formData);
        //check request
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($formData);
            if ($form->isValid()) {
                $postData = $request->getPost();
                $newPerms = explode(',', str_replace(" ", "", $postData['permissions']) );
                $name = $postData['resource_name'];
                //check if name has changed
                if ($resource['resource_name'] != $name) {
                    //update resource
                    $this->resTable->update(['resource_name' => $name], "id = $id");
                }
                // remove old permissions
                foreach ($perms as $perm) {
                    if (!in_array ($perm['permission_name'], $newPerms) ) {
                        $this->permTable->delete('id = ' . $perm["id"]);
                    }
                }

                // add new permissions
                foreach ($newPerms as $perm) {
                    if (!in_array($perm, $permNames) )
                        $this->permTable->add($id, $perm);
                }
                $this->clearCache();
                return $this->redirect()->toRoute('resource');
            } else {
        
            }
        }
        return array(
            'id' => $id,
            'name' => $resource['resource_name'],
            'form' => $form
        );
    }
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
    
        if (! $id) {
            return $this->redirect()->toRoute('resource');
        }
        $res = $this->resTable->getByID($id);
        $request = $this->getRequest();
    
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
    
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->resTable->deleteByID($id);
                $this->permTable->deleteByResourceID($id);
            }
            $this->clearCache();
            // Redirect to list of albums
            return $this->redirect()->toRoute('resource');
        }
        return array(
            'id' => $id,
            'resourcename' => $res['resource_name']
        );
    }
    private function clearCache($type = 'acl'){
        $this->cacheService->clearCache($type);
    }
}