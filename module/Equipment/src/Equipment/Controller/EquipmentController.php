<?php
namespace Equipment\Controller;

use Auth\Service\AccessService;
use Auth\Service\UserService;
use Equipment\Model\Enums\EEquipTypes;
use Equipment\Service\EquipmentService;
use Equipment\Utility\EquipmentDataTable;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;

class EquipmentController extends AbstractActionController
{
    /** @var EquipmentService  */
    private $equipService;
    /** @var UserService  */
    private $userService;
    /** @var AccessService  */
    private $accessService;
    private $config;

    private $dataTable;


    public function __construct($config, EquipmentService $equipmentService, UserService $userService, AccessService $accessService) {
        $controllerName = str_replace("Controller", "", explode("\\", get_class($this))[2]);
        $this->config = $config[$controllerName];
        $this->userService   = $userService;
        $this->accessService = $accessService;
        $this->equipService  = $equipmentService;
        $this->dataTable     = new EquipmentDataTable();
        $this->dataTable->setServices($this->accessService, $this->equipService);
    }

    public function indexAction() {
        $vars = $this->getVars('index');
        $this->dataTable->configure('index', null, $this->equipService->getAll());
        return $vars + array ('dataTable' => $this->dataTable);
    }

    public function typeAction()
    {
        $action = 'type';
        $type = $vars['typeString'] = $this->params()->fromRoute('type');
        $type = $vars['type'] = EEquipTypes::TRANSLATE_TO_ID[strtolower($type)];
        $vars = array_merge_recursive($vars, $this->getVars($action, $vars['typeString']));

        // create data table
        $items = $this->equipService->getAllByType($type)->toArray();
        $this->dataTable->configure($action, $type, $items);
        
        foreach ($items as $item)
            $vars['userList'][$item['userId']] = $item['userName'];

        return array_merge($vars, array(
            'dataTable' => $this->dataTable,
        ));
    }

    public function userallAction(){
        $action = 'userall';
        $type = $vars['typeString'] = $this->params()->fromRoute('type');
        $type = $vars['type'] = EEquipTypes::TRANSLATE_TO_ID[strtolower($type)];
        $userId = (int) $this->params()->fromRoute('userId');
        $vars = array_merge_recursive($vars, $this->getVars($action, $vars['typeString'], $userId));

        // create data table
        $items = $this->equipService->getByUserIdAndType($userId, $type)->toArray();
        $this->dataTable->configure($action, $type, $items);
        foreach ($items as $item)
            $vars['userList'][$item['userId']] = $item['userName'];

        return array_merge($vars, array(
            'dataTable' => $this->dataTable,
        ));
    }

    public function showAction(){
        $action = 'show';
        $vars['typeString'] = $this->params()->fromRoute('type');
        $vars['type'] = EEquipTypes::TRANSLATE_TO_ID[strtolower($vars['typeString'])];
        $equipId = (int) $this->params()->fromRoute('equipId');
        $userId = (int) $this->params()->fromRoute('userId');
        $vars = array_merge_recursive($vars, $this->getVars($action, $vars['typeString'], $userId));

        return array_merge($vars, array(
            'equip' => $this->equipService->getById($equipId),
        ));
    }

    public function deleteAction(){
        $action = 'delete';
        $vars['typeString'] = $this->params()->fromRoute('type');
        $vars['type'] = EEquipTypes::TRANSLATE_TO_ID[strtolower($vars['typeString'])];
        $equipId = (int) $this->params()->fromRoute('equipId');
        $userId = (int) $this->params()->fromRoute('userId');
        $vars = array_merge_recursive($vars, $this->getVars($action, $vars['typeString'], $userId));
        
        $askingUserId = $this->accessService->getUserID();
        $askingRole = $this->accessService->getRole();
        
        if ($userId !== $askingUserId && $askingRole !== 'Administrator') return $this->redirect()->toRoute('home');
        
        $url = "/equip/" .$vars['typeString']. "/$userId/delete/$equipId";
        $equip = $this->equipService->getById($equipId);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            if ($post['del'] == 'Yes' && $equipId == $post['id']){
                $checkItem = $this->equipService->getById($equipId);
                if ($askingUserId !== $checkItem->userId)
                    if ($askingRole !== 'Administrator')
                        return $this->redirect()->toRoute('home');
                $this->equipService->deleteById($equipId);
                return $this->redirect()->toUrl($this->flashMessenger()->getMessages('ref')[0]);
            }
        }

        $ref = ($request->getHeader('Referer')) ? $request->getHeader('Referer')->uri()->getPath() : '/equip';
        $this->flashMessenger()->addMessage($ref, 'ref');
        return array_merge($vars, array(
            'equip' => $equip,
            'url' => $url,
        ));
    }

    public function addAction()
    {
        $action = 'add';
        $type = $vars['typeString'] = $this->params()->fromRoute('type');
        $type = $vars['type'] = EEquipTypes::TRANSLATE_TO_ID[strtolower($type)];
        $userId = (int) $this->params()->fromRoute('userId');
        $vars = array_merge_recursive($vars, $this->getVars($action, $vars['typeString'], $userId));

        /** @var Form $form */
        $form = new $vars['formType'][$type]($this->equipService, $this->userService);
        $form->get('userId')->setValue($userId);
        $request = $this->getRequest();
        if ($request->isPost()) {
            //merge post data and files
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $form->setData($post);
            if ($form->isValid()){
                // push into model for selection in service
                $data = new $vars['model'][$type]($form->getData());

                $this->equipService->save($data);
                return $this->redirect()->toUrl($this->flashMessenger()->getMessages('ref')[0]);
            }
        }

        $ref = ($request->getHeader('Referer')) ? $request->getHeader('Referer')->uri()->getPath() : '/equip';
        $this->flashMessenger()->addMessage($ref, 'ref');
        return array_merge($vars, array(
            'form' => $form,
        ));

    }

    public function editAction(){
        $action = 'edit';
        $vars['typeString'] = $this->params()->fromRoute('type');
        $vars['type'] = EEquipTypes::TRANSLATE_TO_ID[strtolower($vars['typeString'])];
        $userId = (int) $this->params()->fromRoute('userId');
        $equipId = (int) $this->params()->fromRoute('equipId');
        $vars = array_merge_recursive($vars, $this->getVars($action, $vars['typeString'], $userId));

        $form = new $vars['formType'][$vars['type']]($this->equipService, $this->userService);
        $request = $this->getRequest();

        if ($request->isPost()) {
            //merge post data and files
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $form->setData($post);
            if ($form->isValid()){
                $item = new $vars['model'][$vars['type']]($form->getData());
                $this->equipService->save($item);
                return $this->redirect()->toUrl($this->flashMessenger()->getMessages('ref')[0]);
            }
        }

        $ref = ($request->getHeader('Referer')) ? $request->getHeader('Referer')->uri()->getPath() : '/equip';
        $this->flashMessenger()->addMessage($ref, 'ref');

        $equip = $this->equipService->getById($equipId);
        $form->setData($equip->toArray());
        return array_merge($vars, array(
            'form' => $form,
        ));
    }

    private function getVars($action, $typeString = null, $userId = false)
    {
        if (isset($this->config['config'][$action]))
            $page = $this->config['config'][$action] + $this->config['config']['default_actionName'];
        else
            $page = $this->config['config']['default_actionName'];
        if(is_array($page['label']))
            $page['label'] = $page['label'][EEquipTypes::TRANSLATE_TO_ID[$typeString]];

        $vars = $page['vars'];
        $vars['page'] = $page;

        if ($action !=='index') {
            if ($action !== 'type')
                $vars['links']['zurück zur Übersicht'] = "/equip/$typeString";
            if ($action !== 'type' && $action !== 'userall')
                $vars['links']['zurück zur User-Übersicht'] = "/equip/$typeString/$userId";
        }
        return $vars;
    }
}