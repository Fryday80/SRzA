<?php
namespace Equipment\Controller;

use Application\Utility\DataTable;
use Auth\Service\AccessService;
use Auth\Service\UserService;
use Equipment\Form\TentTypeForm;
use Equipment\Model\EnumTentShape;
use Equipment\Model\Tent;
use Equipment\Service\EquipmentService;
use Equipment\Form\TentForm;
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


    public function __construct(EquipmentService $equipmentService, UserService $userService, AccessService $accessService) {
        $this->userService = $userService;
        $this->accessService = $accessService;
        $this->equipService = $equipmentService;
        $this->getConfiguration();
    }

    public function indexAction() {
        $vars = $this->config['functions']['getVars']('index', $this->config);

        return array_merge($vars, array(
        ));
    }

    public function typeAction()
    {
        $action = 'type';
        $type = $this->params()->fromRoute('type');
        $userHash[0] = 'Verein';
        $items = false;
        $vars = $this->config['functions']['getVars']($action, $this->config);
        $vars['page'] = $this->config['functions']['getPageConfig']($action, $this->config);
        $vars['type'] = $type;
        
        $allUsers = $this->userService->getAllUsers();
        foreach ($allUsers->data as $user)
            $userHash[$user->id] = $user->name;

        if($type == 'tent')
            $items = $this->equipService->getAllTents()->toArray();
        $dataTable = $this->getDataTable('typeAction', $type, $items);
        $dataTable->insertLinkButton("/equip/$type/add", 'Neuer Eintrag');

        foreach ($items as $item) {
            $vars['userList'][$item['userId']] = $userHash[$item['userId']];
        }

        return array_merge($vars, array(
            'dataTable' => $dataTable,
        ));
    }

    public function addAction()
    {
        $action = 'add';
        $type = $this->params()->fromRoute('type');
        $userId = (int) $this->params()->fromRoute('userId');
        
        $vars = $this->config['functions']['getVars']($action, $this->config);
        $vars['page'] = $this->config['functions']['getPageConfig']($action, $this->config);
        $vars['type'] = $type;

        /** @var Form $form */
        $form = new $vars['formType'][$type]($this->equipService, $this->userService);
        $form->get('userId')->setValue($userId);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            $form->setData($post);
            if ($form->isValid()){
                $data = new $vars['model'][$type]($form->getData());
                
                $this->equipService->saveTent($data);
                $this->redirect()->toUrl('/equip/$type');
            }
        }
        return array_merge($vars, array(
            'form' => $form,
        ));

    }

    public function userallAction(){
        $action = 'userall';
        $type = $this->params()->fromRoute('type');
        $userId = (int) $this->params()->fromRoute('userId');

        $vars = $this->config['functions']['getVars']($action, $this->config);
        $vars['page'] = $this->config['functions']['getPageConfig']($action, $this->config);
        $vars['links']['zurück zur Übersicht'] = "/equip/$type";
        $vars['type'] = $type;
        
        $allUsers = $this->userService->getAllUsers();
        foreach ($allUsers->data as $user)
            $userHash[$user->id] = $user->name;

        $items = $this->equipService->getAllTents()->toArray();
        foreach ($items as $item) {
            $vars['userList'][$item['userId']] = $userHash[$item['userId']];
        }

        if($type == 'tent')
            $items = $this->equipService->getTentsByUserId($userId)->toArray();
        
        $dataTable = $this->getDataTable($action, $type, $items);
        $dataTable->insertLinkButton('/equip/tent/add/' . $userId, 'Neuer Eintrag');

        
        return array_merge($vars, array(
            'dataTable' => $dataTable,
        ));
    }

    public function showAction(){
        $action = 'show';
        $type = $this->params()->fromRoute('type');
        $equipId = (int) $this->params()->fromRoute('equipId');
        $userId = (int) $this->params()->fromRoute('userId');

        $vars = $this->config['functions']['getVars']($action, $this->config);
        $vars['page'] = $this->config['functions']['getPageConfig']($action, $this->config);
        $vars['links']['zurück zur Übersicht'] = "/equip/$type";
        $vars['links']['zurück zur User-Übersicht'] = "/equip/$type/$userId";
        $vars['type'] = $type;

        return array_merge($vars, array(
            'tent' => $this->equipService->getById($equipId),
        ));
    }

    public function deleteAction(){
        $action = 'delete';
        $type = $this->params()->fromRoute('type');
        $equipId = (int) $this->params()->fromRoute('equipId');
        $userId = (int) $this->params()->fromRoute('userId');

        $vars = $this->config['functions']['getVars']($action, $this->config);
        $vars['page'] = $this->config['functions']['getPageConfig']($action, $this->config);
        $vars['links']['zurück zur Übersicht'] = "/equip/$type";
        $vars['links']['zurück zur User-Übersicht'] = "/equip/$type/$userId";
        $vars['type'] = $type;
        
        $askingUserId = $this->accessService->getUserID();
        $askingRole = $this->accessService->getRole();
        
        if ($userId !== $askingUserId && $askingRole !== 'Administrator') return $this->redirect()->toRoute('home');
        
        $url = "/equip/$type/$userId/delete/$equipId";
        $equip = $this->equipService->getById($equipId);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            if ($post['del'] == 'Yes' && $equipId == $post['id']){
                $checkTent = $this->equipService->getTentById($equipId);
                if ($askingUserId !== $checkTent->userId)
                    if ($askingRole !== 'Administrator')
                        return $this->redirect()->toRoute('home');
                $this->equipService->deleteById($equipId);
                return $this->redirect()->toUrl("/equip/$type/$userId");
            }
        }
        return array_merge($vars, array(
            'equip' => $equip,
            'url' => $url,
        ));
    }

    public function editAction(){
        $action = 'edit';
        $type = $this->params()->fromRoute('type');
        $equipId = (int) $this->params()->fromRoute('equipId');
        $userId = (int) $this->params()->fromRoute('userId');

        $vars = $this->config['functions']['getVars']($action, $this->config);
        $vars['page'] = $this->config['functions']['getPageConfig']($action, $this->config);
        $vars['links']['zurück zur Übersicht'] = "/equip/$type";
        $vars['links']['zurück zur User-Übersicht'] = "/equip/$type/$userId";
        $vars['type'] = $type;
        
        $equip = $this->equipService->getById($equipId);

        $form = new $vars['formType'][$type]($this->equipService, $this->userService);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            $form->setData($post);
            if ($form->isValid()){
                $tent = new $vars['model'][$type]($form->getData());
                $this->equipService->saveTent($tent);
                return $this->redirect()->toUrl("/equip/$type");
            }
        }
        $form->setData($equip->toArray());
        return array_merge($vars, array(
            'form' => $form,
        ));
    }

    //@todo
//    public function edittenttypeAction(){
//        $typeId = (int) $this->params()->fromRoute('id');
//        $links = array(
//            array(
//                'name' => 'zurück zur Managerübersicht',
//                'url'  => '/equip',
//            ),
//            array(
//                'name' => 'zurück zur Zeltverwaltung',
//                'url'  => '/equip/tent',
//            ),
////            array(
////                'name' => 'zurück zur User-Übersicht',
////                'url'  => "/equip/tent/$userId",
////            ),
//        );
//        $tent = $this->equipService->getTypesByID($typeId);
//
//        $form = new TentTypeForm($this->equipService, $this->userService);
//        $request = $this->getRequest();
//        if ($request->isPost()) {
//            $post = $request->getPost();
//            $form->setData($post);
//            if ($form->isValid()){
//                $tent = new Tent($form->getData());
//                $this->equipService->saveTent($tent);
//            }
//        }
//        $form->setData($tent->toArray());
//        return array(
//            'form' => $form,
//            'links' => $links,
//        );
//    }

    private function getConfiguration()
    {
        $this->config = include_once (getcwd(). '\module\Equipment\config\EquipManager.config.php');
    }

    private function getDataTable($action, $type, $items)
    {
        switch ($type) {
            case 'tent':
                $dataTableVarColumns = array();
                if($action == 'typeAction'){
                    $dataTableVarColumns[] = array(
                        'name' => 'readableUser',
                        'label' => 'Von',
                        'type' => 'custom',
                        'render' => function ($row) {
                            return ($row['userId'] == 0) ? 'Verein' : $this->userService->getUserNameByID($row['userId']);
                        }
                    );
                }


                $columns = array(
                    array(
                        'name' => 'image', 'label' => 'Form', 'type' => 'custom',
                        'render' => function ($row) {
                            return '<img alt="' . EnumTentShape::TRANSLATION[$row['shape']] . '" src="' . $row['image'] . '" style="width: 50px">';
                        }
                    ),
                    array(
                        'name' => 'readableType', 'label' => 'Typ', 'type' => 'custom',
                        'render' => function ($row) {
                            return ($row['type'] == 0) ? 'Sonstige' : $this->equipService->getTypeNameById($row['type']);
                        }
                    ),
                    array(
                        'name' => 'width', 'label' => 'Breite'
                    ),
                    array(
                        'name' => 'length', 'label' => 'Tiefe'
                    ),
                    array(
                        'name' => 'spareBeds', 'label' => 'freie<br/>Schlaf-<br/>plätze'
                    ),
                    array(
                        'name' => 'colorField', 'label' => 'Farbe', 'type' => 'custom',
                        'render' => function ($row) {
                            $c1 = $row['color1'];
                            $c2 = $row['color2'];
                            return '<div style="
                                            width: 0;
                                            height: 0;
                                            border-left:   20px solid ' . $c1 . ';
                                            border-top:    20px solid ' . $c1 . ';
                                            border-right:  20px solid ' . $c2 . ';
                                            border-bottom: 20px solid ' . $c2 . ';
                                            "></div>';
                        }
                    ),
                    array(
                        'name' => 'isShowTentValue', 'label' => 'Schauzelt?', 'type' => 'custom',
                        'render' => function ($row) {
                            return ($row['isShowTent'] == 0) ? 'nein' : 'ja';
                        }
                    ),
                    array(
                        'name' => 'href',
                        'label' => 'Aktion',
                        'type' => 'custom',
                        'render' => function ($row) {
                            $edit = '';
                            $delete = '';
                            $askingId = $this->accessService->getUserID();
                            $askingRole = $this->accessService->getRole();
                            $link1 = '<a href="/equip/tent/' . $row['userId'] . '/show/' . $row['id'] . '">Details</a>';
                            if ($row['userId'] == $askingId || $askingRole == 'Administrator') {
                                $edit = '<a href="/equip/tent/' . $row['userId'] . '/edit/' . $row['id'] . '">Edit</a>';
                                $delete = '<a href="/equip/tent/' . $row['userId'] . '/delete/' . $row['id'] . '">Delete</a>';
                            }
                            return $link1 . '<br/>' . $edit . '<br/>' . $delete;
                        }
                    ),
                );
                $dtColumns = array_merge_recursive($dataTableVarColumns, $columns);
                return new DataTable(array(
                    'data' => $items,
                    'columns' => $dtColumns
                ));
                break;
        }
    }
}
