<?php
namespace Nav\Controller;

use Application\Service\CacheService;
use Auth\Model\Tables\RoleTable;
use Nav\Model\Tables\NavTable;
use Zend\Mvc\Controller\AbstractActionController;
use Nav\Form\NavForm;
use Zend\Json\Json;

class NavController extends AbstractActionController
{

    /** @var $cacheService CacheService */
    private $cacheService;
    /** @var $navTable NavTable */
    private $navTable;
    /** @var $roleTable RoleTable */
    private $roleTable;
    /** @var  Array */
    private $config;

    public function __construct(CacheService $cacheService, NavTable $navTable, RoleTable $roleTable, Array $conf)
    {
        $this->cacheService = $cacheService;
        $this->navTable = $navTable;
        $this->roleTable = $roleTable;
        $this->config = $conf;
    }
    public function indexAction()
    {
    }

    public function addAction()
    {
        $form = new NavForm($this->roleTable->fetchAllSorted());
        $form->get('submit')->setValue('Add');
        $routes = $this->config['router']['routes'];

        $routeNames = array();
        foreach ($routes as $name => $route) {
            if (array_key_exists('child_routes', $route)) {
                foreach ($route['child_routes'] as $childName => $childRoute) {
                    if ($childName == 'default')
                        continue;
                    array_push($routeNames, "$name/$childName");
                }
            } else {
                array_push($routeNames, $name);
            }
        }
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $this->navTable->append($data);
                //clear Cache
                $this->clearCache();
                // Redirect
                return $this->redirect()->toRoute('nav/sort');
            }
        }
        return array(
            'form' => $form
        );
    }

    public function editAction()
    {
        $itemID = (int) $this->params('id');
        if (! $itemID) {
            return $this->redirect()->toRoute('nav/sort');
        }
        try {
            $navItem = $this->navTable->getItem($itemID);
            if ($navItem === false) {
                throw new \Exception('No Nav Item found with this id!');
            }
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('nav/sort');
        }
        $form = new NavForm($this->roleTable->fetchAllSorted());
        // $form->get('permission_id')->setValue($navItem['permission_id']);
        $form->get('submit')->setValue('Edit');
        
        $form->populateValues($navItem);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $this->navTable->updateItem(array(
                    'label' => $data['label'],
                    'uri' => $data['uri'],
                    'target' => $data['target'],
                    'min_role_id' => $data['min_role_id']
                ), $data['id']);
                //clear Cache
                $this->clearCache();
                // Redirect
                return $this->redirect()->toRoute('nav/sort');
            }
        }
        return array(
            'id' => $itemID,
            'form' => $form
        );
    }

    public function sortAction()
    {
        $form = new NavForm($this->roleTable->fetchAllSorted());
        $form->get('submit')->setValue('Edit');

        $navTree = $this->navTable->getNav(0);
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $this->request->getContent();
            $data = Json::decode($data, Json::TYPE_ARRAY);

            print('{"error":"');
            $lft = 1;
            $rgt = 2;
            $result = [];
            $recursive = null;
            $isJSON = (preg_match('/application\/json/', $this->getRequest()
                ->getHeaders()
                ->get('Accept')
                ->getFieldValue()) !== false) ? true : false;

            $recursive = function ($data) use (&$lft, &$rgt, &$result, &$recursive) {
                if (array_key_exists('children', $data) && count($data['children']) > 0) {
                    $myLft = $lft ++;
                    foreach ($data['children'] as $child) {
                        $recursive($child);
                    }
                    $rgt = $lft;
                    $lft = $myLft;
                } else {
                    $rgt = $lft + 1;
                }
                array_push($result, array(
                    'id' => $data['id'],
                    'lft' => $lft,
                    'rgt' => $rgt
                ));
                $lft = $rgt + 1;
            };
            foreach ($data as $node) {
                $recursive($node);
            }

            // update database
            foreach ($result as $row) {
                $this->navTable->updateNesting($row);
            }
            // clear cache

            $this->clearCache();
            
            print('"}');
            die();
        }
        
        return array(
            'id' => 0,
            'navtree' => $navTree,
            'editForm' => $form
        );
    }

    public function deleteAction()
    {
        // check for param id
        $id = (int) $this->params()->fromRoute('id', 0);
        if (! $id) {
            return $this->redirect()->toRoute('nav/sort');
        }
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
            
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->navTable->deleteByID($id);
            }
            //clear Cache
            $this->clearCache();
            // Redirect
            return $this->redirect()->toRoute('nav/sort');
        }
        return array(
            'id' => $id,
            'item' => $this->navTable->getItem($id)
        );
    }
    
    private function clearCache($type = 'nav/main'){
        $this->cacheService->clearCache('nav/main');
    }
}
