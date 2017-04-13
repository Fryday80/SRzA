<?php
namespace Nav\Controller;

use Application\Service\CacheService;
use Nav\Service\NavService;
use Zend\Mvc\Controller\AbstractActionController;
use Nav\Form\NavForm;
use Zend\Json\Json;

class NavController extends AbstractActionController
{

    protected $albumTable;

    /** @var $cacheService CacheService */
    private $cacheService = false;
    private $cache;

    public function indexAction()
    {
        // show all menus
        // link to add/edit/delete

        // $navTable = $this->getServiceLocator()->get('Nav\Model\NavTable');
        // $navTable->getNav(0);
        // return new ViewModel(array(
        // 'albums' => $this->getAlbumTable()->fetchAll()
        // ));
    }

    public function addAction()
    {
        $this->getCache();
        $navTable = $this->getServiceLocator()->get("Nav\Model\NavTable");
        $roleTable = $this->getServiceLocator()->get("Auth\Model\RoleTable");
        $form = new NavForm($roleTable->fetchAllSorted());
        $form->get('submit')->setValue('Add');
        
        $config = $this->getServiceLocator()->get('Config');
        $routes = $config['router']['routes'];

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
                $navTable->append($data);
                //clear Cache
                $this->cacheService->clearCache('nav/main');
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
        $this->getCache();
        $itemID = (int) $this->params('id');
        if (! $itemID) {
            return $this->redirect()->toRoute('nav/sort');
        }
        $navTable = $this->getServiceLocator()->get("Nav\Model\NavTable");
        try {
            $navItem = $navTable->getItem($itemID);
            if ($navItem === false) {
                throw new \Exception('No Nav Item found with this id!');
            }
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('nav/sort');
        }
        $roleTable = $this->getServiceLocator()->get("Auth\Model\RoleTable");
        $form = new NavForm($roleTable->fetchAllSorted());
        // $form->get('permission_id')->setValue($navItem['permission_id']);
        $form->get('submit')->setValue('Edit');
        
        $form->populateValues($navItem);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $navTable->updateItem(array(
                    'label' => $data['label'],
                    'uri' => $data['uri'],
                    'permission_id' => $data['permission_id']
                ), $data['id']);
                //clear Cache
                $this->cacheService->clearCache('nav/main');
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
        $this->getCache();
        $roleTable = $this->getServiceLocator()->get("Auth\Model\RoleTable");
        $form = new NavForm($roleTable->fetchAllSorted());
        $form->get('submit')->setValue('Edit');
        
        $NavTable = $this->getServiceLocator()->get("Nav\Model\NavTable");
        $navTree = $NavTable->getNav(0);
        
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
                $NavTable->updateNesting($row);
            }
            
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
        $this->getCache();
        // check for param id
        $id = (int) $this->params()->fromRoute('id', 0);
        if (! $id) {
            return $this->redirect()->toRoute('nav/sort');
        }
        $navTable = $this->getServiceLocator()->get("Nav\Model\NavTable");
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
            
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $navTable->deleteByID($id);
            }
            //clear Cache
            $this->cacheService->clearCache('nav/main');
            // Redirect
            return $this->redirect()->toRoute('nav/sort');
        }
        return array(
            'id' => $id,
            'item' => $navTable->getItem($id)
        );
    }

    /**
     * get the CacheService
     * sets the nav cache in $this->cache
     */
    private function getCache() {
        if (!$this->cacheService) {
            $this->cacheService = $this->getServiceLocator()->get('CacheService');
            $this->cache = $this->cacheService->getCache('nav/main');
        }
    }
}
