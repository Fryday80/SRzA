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
use Zend\Mvc\Controller\AbstractActionController;

class EquipmentController extends AbstractActionController
{
    /** @var EquipmentService  */
    private $equipService;
    /** @var UserService  */
    private $userService;
    /** @var AccessService  */
    private $accessService;


    public function __construct(EquipmentService $equipmentService, UserService $userService, AccessService $accessService) {
        $this->userService = $userService;
        $this->accessService = $accessService;
        $this->equipService = $equipmentService;
    }

    public function indexAction() {
        $managerSites = $this->getConfiguration();
        
        return array(
            'sites' => $managerSites,
        );
    }

    //=================================== Tent
    public function tentAction()
    {
        $links = array(
            array(
                'name' => 'zurück zur Übersicht',
                'url'  => '/equip',
            ),
        );
        $site = $this->getConfiguration()['tent'];

        $dataTable = new DataTable(array(
            'data' => $this->equipService->getAllTents()->toArray(),
            'columns' => array(
                array(
                    'name' => 'readableUser', 'label' => 'Von', 'type' => 'custom',
                    'render' => function ($row) {
                        return ($row['userId'] == 0) ? 'Verein' : $this->userService->getUserNameByID($row['userId']);
                    }
                ),
                array (
                    'name' => 'image', 'label' => 'Form', 'type' => 'custom',
                    'render' => function ($row){
                        return '<img alt="' . EnumTentShape::TRANSLATION[$row['shape']] . '" src="' . $row['image'] . '" style="width: 50px">';
                    }
                ),
                array (
                    'name' => 'readableType', 'label' => 'Typ', 'type' => 'custom',
                    'render'=> function($row){
                        return ($row['type'] == 0) ? 'Sonstige' : $this->equipService->getTypeNameById($row['type']);
                    }
                ),
                array (
                    'name' => 'width', 'label' => 'Breite'
                ),
                array (
                    'name' => 'length', 'label' => 'Tiefe'
                ),
                array (
                    'name' => 'spareBeds', 'label' => 'freie<br/>Schlaf-<br/>plätze'
                ),
                array (
                    'name' => 'colorField', 'label' => 'Farbe', 'type' => 'custom',
                    'render' => function($row){
                        $c1 = $row['color1'];
                        $c2 = $row['color2'];
                        return '<div style="
                                    width: 0;
                                    height: 0;
                                    border-left:   20px solid ' .$c1. ';
                                    border-top:    20px solid ' .$c1. ';
                                    border-right:  20px solid ' .$c2. ';
                                    border-bottom: 20px solid ' .$c2. ';
                                    "></div>';
                    }
                ),
                array (
                    'name' => 'isShowTentValue', 'label' => 'Schauzelt?', 'type' => 'custom',
                    'render' => function($row){
                        return ($row['isShowTent'] == 0) ? 'nein' : 'ja';
                    }
                ),
                array (
                    'name'  => 'href',
                    'label' => 'Aktion',
                    'type'  => 'custom',
                    'render' => function ($row){
                        $edit   = '';
                        $delete = '';
                        $askingId = $this->accessService->getUserID();
                        $askingRole = $this->accessService->getRole();
                        $link1 = '<a href="/equip/tent/' . $row['userId'] . '">Alle des Users</a>';
                        $link2 = '<a href="/equip/tent/' . $row['userId'] . '/show/' . $row['id'] . '">Details</a>';
                        if ( $row['userId'] == $askingId || $askingRole == 'Administrator' ) {
                            $edit   = '<a href="/equip/tent/' . $row['userId'] . '/edit/' . $row['id'] . '">Edit</a>';
                            $delete = '<a href="/equip/tent/' . $row['userId'] . '/delete/' . $row['id'] . '">Delete</a>';
                        }
                        return $link1. '<br/>' .$link2. '<br/>' .$edit.'<br/>'.$delete;
                    }
                ),
            )
        ));
        $dataTable->insertLinkButton('/equip/tent/add', 'Neues Zelt');

        return array(
            'site' => $site,
            'dataTable' => $dataTable,
            'links' => $links,
        );

    }
    public function usertentallAction(){
        $links = array(
            array(
                'name' => 'zurück zur Managerübersicht',
                'url'  => '/equip',
            ),
            array(
                'name' => 'zurück zur Zeltverwaltung',
                'url'  => '/equip/tent',
            ),
        );
        $userId = (int) $this->params()->fromRoute('userId');

        $dataTable = new DataTable(array(
            'data' => $this->equipService->getTentsByUserId($userId)->toArray(),
            'columns' => array(

                array (
                    'name' => 'image', 'label' => 'Form', 'type' => 'custom',
                    'render' => function ($row){
                        return '<img alt="' . EnumTentShape::TRANSLATION[$row['shape']] . '" src="' . $row['image'] . '" style="width: 50px">';
                    }
                ),
                array (
                    'name' => 'readableType', 'label' => 'Typ', 'type' => 'custom',
                    'render'=> function($row){
                        return ($row['type'] == 0) ? 'Sonstige' : $this->equipService->getTypeNameById($row['type']);
                    }
                ),
                array (
                    'name' => 'width', 'label' => 'Breite'
                ),
                array (
                    'name' => 'length', 'label' => 'Tiefe'
                ),
                array (
                    'name' => 'spareBeds', 'label' => 'freie<br/>Schlaf-<br/>plätze'
                ),
                array (
                    'name' => 'colorField', 'label' => 'Farbe', 'type' => 'custom',
                    'render' => function($row){
                        $c1 = $row['color1'];
                        $c2 = $row['color2'];
                        return '<div style="
                                    width: 0;
                                    height: 0;
                                    border-left:   20px solid ' .$c1. ';
                                    border-top:    20px solid ' .$c1. ';
                                    border-right:  20px solid ' .$c2. ';
                                    border-bottom: 20px solid ' .$c2. ';
                                    "></div>';
                    }
                ),
                array (
                    'name' => 'isShowTentValue', 'label' => 'Schauzelt?', 'type' => 'custom',
                    'render' => function($row){
                        return ($row['isShowTent'] == 0) ? 'nein' : 'ja';
                    }
                ),
                array (
                    'name'  => 'href',
                    'label' => 'Aktion',
                    'type'  => 'custom',
                    'render' => function ($row){
                        $edit   = '';
                        $delete = '';
                        $askingId = $this->accessService->getUserID();
                        $askingRole = $this->accessService->getRole();
                        $link1 = '<a href="/equip/tent/' . $row['userId'] . '/show/' . $row['id'] . '">Details</a>';
                        if ( $row['userId'] == $askingId || $askingRole == 'Administrator' ) {
                            $edit   = '<a href="/equip/tent/' . $row['userId'] . '/edit/' . $row['id'] . '">Edit</a>';
                            $delete = '<a href="/equip/tent/' . $row['userId'] . '/delete/' . $row['id'] . '">Delete</a>';
                        }
                        return $link1. '<br/>' .$edit.'<br/>'.$delete;
                    }
                ),
            )
        ));
        $dataTable->insertLinkButton('/equip/tent/add/' . $userId, 'Neues Zelt');
        return array(
            'dataTable' => $dataTable,
            'links' => $links,
        );
    }
    public function usertentAction(){
        $tentId = (int) $this->params()->fromRoute('tentId');
        $userId = (int) $this->params()->fromRoute('userId');
        $links = array(
            array(
                'name' => 'zurück zur Managerübersicht',
                'url'  => '/equip',
            ),
            array(
                'name' => 'zurück zur Zeltverwaltung',
                'url'  => '/equip/tent',
            ),
            array(
                'name' => 'zurück zur User-Übersicht',
                'url'  => "/equip/tent/$userId",
            ),
        );
        return array(
            'tent' => $this->equipService->getTentById($tentId),
            'links' => $links,
        );
    }
    public function deletetentAction(){
        $tentId = (int) $this->params()->fromRoute('tentId');
        $userId = (int) $this->params()->fromRoute('userId');
        $links = array(
            array(
                'name' => 'zurück zur Managerübersicht',
                'url'  => '/equip',
            ),
            array(
                'name' => 'zurück zur Zeltverwaltung',
                'url'  => '/equip/tent',
            ),
            array(
                'name' => 'zurück zur User-Übersicht',
                'url'  => "/equip/tent/$userId",
            ),
        );
        $askingUserId = $this->accessService->getUserID();
        $askingRole = $this->accessService->getRole();
        if ($userId !== $askingUserId && $askingRole !== 'Administrator') return $this->redirect()->toRoute('home');
        $url = "/equip/tent/$userId/delete/$tentId";
        $tent = $this->equipService->getTentById($tentId);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            if ($post['del'] == 'Yes' && $tentId == $post['id']){
                $checkTent = $this->equipService->getTentById($tentId);
                if ($askingUserId !== $checkTent->userId)
                    if ($askingRole !== 'Administrator')
                        return $this->redirect()->toRoute('home');
                $this->equipService->deleteTentById($tentId);
                return $this->redirect()->toRoute('equipmanager/tent');
            }
        }
        return array(
            'tent' => $tent,
            'url' => $url,
            'links' => $links,
        );
    }
    public function addtentAction()
    {
        $userId = (int) $this->params()->fromRoute('userId');
        $links = array(
            array(
                'name' => 'zurück zur Managerübersicht',
                'url'  => '/equip',
            ),
            array(
                'name' => 'zurück zur Zeltverwaltung',
                'url'  => '/equip/tent',
            ),
        );
        $form = new TentForm($this->equipService, $this->userService);
        $form->get('userId')->setValue($userId);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            $form->setData($post);
            if ($form->isValid()){
                $data = new Tent($form->getData());
                $this->equipService->saveTent($data);
                $this->redirect()->toRoute('equipmanager/tent');
            }
        }
        return array(
            'form' => $form,
            'links' => $links,
        );

    }
    public function edittentAction(){
        $tentId = (int) $this->params()->fromRoute('tentId');
        $userId = (int) $this->params()->fromRoute('userId');
        $links = array(
            array(
                'name' => 'zurück zur Managerübersicht',
                'url'  => '/equip',
            ),
            array(
                'name' => 'zurück zur Zeltverwaltung',
                'url'  => '/equip/tent',
            ),
            array(
                'name' => 'zurück zur User-Übersicht',
                'url'  => "/equip/tent/$userId",
            ),
        );
        $tent = $this->equipService->getTentById($tentId);

        $form = new TentForm($this->equipService, $this->userService);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            $form->setData($post);
            if ($form->isValid()){
                $tent = new Tent($form->getData());
                $this->equipService->saveTent($tent);
                return $this->redirect()->toRoute('equipmanager/tent');
            }
        }
        $form->setData($tent->toArray());
        return array(
            'form' => $form,
            'links' => $links,
        );
    }
    public function edittenttypeAction(){
        $typeId = (int) $this->params()->fromRoute('id');
        $links = array(
            array(
                'name' => 'zurück zur Managerübersicht',
                'url'  => '/equip',
            ),
            array(
                'name' => 'zurück zur Zeltverwaltung',
                'url'  => '/equip/tent',
            ),
//            array(
//                'name' => 'zurück zur User-Übersicht',
//                'url'  => "/equip/tent/$userId",
//            ),
        );
        $tent = $this->equipService->getTypesByID($typeId);

        $form = new TentTypeForm($this->equipService, $this->userService);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            $form->setData($post);
            if ($form->isValid()){
                $tent = new Tent($form->getData());
                $this->equipService->saveTent($tent);
            }
        }
        $form->setData($tent->toArray());
        return array(
            'form' => $form,
            'links' => $links,
        );
    }

    private function getConfiguration()
    {
        return include_once (getcwd(). '\module\Equipment\config\EquipManager.config.php');
    }
}
