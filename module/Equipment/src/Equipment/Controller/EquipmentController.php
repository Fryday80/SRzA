<?php
namespace Equipment\Controller;

use Application\Utility\DataTable;
use Auth\Service\AccessService;
use Auth\Service\UserService;
use Equipment\Form\TentTypeForm;
use Equipment\Model\EnumEquipTypes;
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

        return $vars;
    }

    public function typeAction()
    {
        $action = 'type';
        $type = $this->params()->fromRoute('type');
        $type = EnumEquipTypes::TRANSLATE_TO_ID[strtolower($type)];
        $vars = $this->getVars($action, $type);

        // create data table
        $items = $this->getDataItems($action, $type);
        $dataTable = $this->getDataTable($action, $type, $items);
        
        $vars = $this->getUserList($type, $vars);

        return array_merge($vars, array(
            'dataTable' => $dataTable,
        ));
    }

    public function addAction()
    {
        $action = 'add';
        $type = $this->params()->fromRoute('type');
        $type = EnumEquipTypes::TRANSLATE_TO_ID[strtolower($type)];
        $userId = (int) $this->params()->fromRoute('userId');
        $vars = $this->getVars($action, $type);

        /** @var Form $form */
        $form = new $vars['formType'][$type]($this->equipService, $this->userService);
        $form->get('userId')->setValue($userId);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            $form->setData($post);
            if ($form->isValid()){
                $data = new $vars['model'][$type]($form->getData());
                
                $this->equipService->save($data);
                $this->redirect()->toUrl('/equip/'. $vars['typeString']);
            }
        }
        return array_merge($vars, array(
            'form' => $form,
        ));

    }

    public function userallAction(){
        $action = 'userall';
        $type = $this->params()->fromRoute('type');
        $type = EnumEquipTypes::TRANSLATE_TO_ID[strtolower($type)];
        $userId = (int) $this->params()->fromRoute('userId');
        $vars = $this->getVars($action, $type, $userId);

        // create data table
        $items = $this->getDataItems($action, $type, $userId);
        $dataTable = $this->getDataTable($action, $type, $items);
        $vars = $this->getUserList($type, $vars);

        return array_merge($vars, array(
            'dataTable' => $dataTable,
        ));
    }

    public function showAction(){
        $action = 'show';
        $type = $this->params()->fromRoute('type');
        $type = EnumEquipTypes::TRANSLATE_TO_ID[strtolower($type)];
        $equipId = (int) $this->params()->fromRoute('equipId');
        $userId = (int) $this->params()->fromRoute('userId');

        $vars = $this->getVars($action, $type, $userId);

        return array_merge($vars, array(
            'equip' => $this->equipService->getById($equipId),
        ));
    }

    public function deleteAction(){
        $action = 'delete';
        $type = $this->params()->fromRoute('type');
        $type = EnumEquipTypes::TRANSLATE_TO_ID[strtolower($type)];
        $equipId = (int) $this->params()->fromRoute('equipId');
        $userId = (int) $this->params()->fromRoute('userId');

        $vars = $this->getVars($action, $type, $userId);
        
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
                return $this->redirect()->toUrl("/equip/". $vars['typeString'] ."/$userId");
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
        $type = EnumEquipTypes::TRANSLATE_TO_ID[strtolower($type)];
        $userId = (int) $this->params()->fromRoute('userId');
        $equipId = (int) $this->params()->fromRoute('equipId');

        $vars = $this->getVars($action, $type, $userId);
        
        $equip = $this->equipService->getById($equipId);

        $form = new $vars['formType'][$type]($this->equipService, $this->userService);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            $form->setData($post);
            if ($form->isValid()){
                $tent = new $vars['model'][$type]($form->getData());
                $this->equipService->saveTent($tent);
                return $this->redirect()->toUrl("/equip/" . $vars['typeString'] . "/$userId");
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

    private function getVars($action, $type, $userId = false)
    {
        $vars = $this->config['functions']['getVars']($action, $this->config);
        $vars['page'] = $this->config['functions']['getPageConfig']($action, $this->config);
        $vars['type'] = $type;
        $vars['typeString'] = $typeString = EnumEquipTypes::TRANSLATE_TO_STRING[$type];
        $vars['links']['zurück zur Übersicht'] = "/equip/$typeString";
        if ($userId)
            $vars['links']['zurück zur User-Übersicht'] = "/equip/$typeString/$userId";
        return $vars;
    }

    private function getDataItems($action, $type, $userId = null)
    {
        $items = false;
        switch ($action){
            case 'type';
                $items = $this->equipService->getAllByType($type)->toArray();
                break;
            case 'userall':
                $items = $this->equipService->getByUserIdAndType($userId, $type)->toArray();
                break;
        }
        return $items;
    }

    private function getDataTable($action, $type, $items)
    {
        $dataTableVarColumns = array();
        if($action == 'type'){
            $dataTableVarColumns[] = array(
                'name' => 'readableUser',
                'label' => 'Von',
                'type' => 'custom',
                'render' => function ($row) {
                    return ($row['userId'] == 0) ? 'Verein' : $this->userService->getUserNameByID($row['userId']);
                }
            );
        }

        if($type == EnumEquipTypes::TENT)
        $columns = array(
            array(
                'name' => 'name', 'label' => 'Name'
            ),
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

        if($type == EnumEquipTypes::EQUIPMENT)
        $columns = array(
            array(
                'name' => 'name', 'label' => 'Name'
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
                'name' => 'colorField', 'label' => 'Farbe', 'type' => 'custom',
                'render' => function ($row) {
                    $c1 = $row['color'];
                    $c2 = $row['color'];
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
                'name' => 'href',
                'label' => 'Aktion',
                'type' => 'custom',
                'render' => function ($row) {
                    $edit = '';
                    $delete = '';
                    $askingId = $this->accessService->getUserID();
                    $askingRole = $this->accessService->getRole();
                    $link1 = '<a href="/equip/equipment/' . $row['userId'] . '/show/' . $row['id'] . '">Details</a>';
                    if ($row['userId'] == $askingId || $askingRole == 'Administrator') {
                        $edit = '<a href="/equip/equipment/' . $row['userId'] . '/edit/' . $row['id'] . '">Edit</a>';
                        $delete = '<a href="/equip/equipment/' . $row['userId'] . '/delete/' . $row['id'] . '">Delete</a>';
                    }
                    return $link1 . '<br/>' . $edit . '<br/>' . $delete;
                }
            ),
        );
        $dtColumns = array_merge_recursive($dataTableVarColumns, $columns);
        $dataTable = new DataTable(array(
            'data' => $items,
            'columns' => $dtColumns
        ));

        $typeString = EnumEquipTypes::TRANSLATE_TO_STRING[$type];
        $dataTable->insertLinkButton("/equip/$typeString/add", 'Neuer Eintrag');
        return $dataTable;
    }

    private function getUserList($type, $vars)
    {
        $allUsers = $this->userService->getAllUsers();
        $userHash[0] = 'Verein';

        foreach ($allUsers->data as $user)
            $userHash[$user->id] = $user->name;

        $items = $this->equipService->getAllByType($type)->toArray();
        foreach ($items as $item)
            $vars['userList'][$item['userId']] = $userHash[$item['userId']];
        return $vars;
    }
}
