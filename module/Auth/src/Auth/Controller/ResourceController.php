<?php
namespace Auth\Controller;

use Application\Service\CacheService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Auth\Form\ResourceForm;

class ResourceController extends AbstractActionController
{
    /** @var CacheService */
    private $cache;

    function __construct()
    {
        $this->cache = $this->getServiceLocator()->get("CacheService");
    }

    public function indexAction()
    {
        $resTable = $this->getServiceLocator()->get("Auth\Model\ResourceTable");
        
        return array(
            'resources' => $resTable->getAllResources()
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
                $resTable = $this->getServiceLocator()->get("Auth\Model\ResourceTable");
                $resID = $resTable->add($name);
                //create new permissions
                $permTable = $this->getServiceLocator()->get("Auth\Model\PermissionTable");
                foreach ($perms as $value) {
                    $permTable->add($resID, $value);
                }
                $this->cache->clearCache('acl');
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
        $resTable = $this->getServiceLocator()->get("Auth\Model\ResourceTable");
        $resource = $resTable->getByID($id);
        //@todo error if resource with id dosen't exists
        $permTable = $this->getServiceLocator()->get("Auth\Model\PermissionTable");
        $perms = $permTable->getByResourceID($id);
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
                    $resTable->update(['resource_name' => $name], "id = $id");
                }
                // remove old permissions
                foreach ($perms as $perm) {
                    if (!in_array ($perm['permission_name'], $newPerms) ) {
                        $permTable->delete('id = ' . $perm["id"]);
                    }
                }

                // add new permissions
                foreach ($newPerms as $perm) {
                    if (!in_array($perm, $permNames) )
                        $permTable->add($id, $perm);
                }
                $this->cache->clearCache('acl');
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
    
        $permTable = $this->getServiceLocator()->get("Auth\Model\PermissionTable");
        $resTable = $this->getServiceLocator()->get("Auth\Model\ResourceTable");
        $res = $resTable->getByID($id);
        $request = $this->getRequest();
    
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
    
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $resTable->deleteByID($id);
                $permTable->deleteByResourceID($id);
            }

            $this->cache->clearCache('acl');
            // Redirect to list of albums
            return $this->redirect()->toRoute('resource');
        }
        return array(
            'id' => $id,
            'resourcename' => $res['resource_name']
        );
    }
}