<?php
namespace Equipment\Controller;

use Application\Utility\DataTable;
use Auth\Service\AccessService;
use Auth\Service\UserService;
use Equipment\Form\TentTypeForm;
use Equipment\Model\Tent;
use Equipment\Service\TentService;
use Equipment\Form\TentForm;
use Zend\Mvc\Controller\AbstractActionController;

class EquipmentController extends AbstractActionController
{

    /** @var TentService  */
    private $tentService;
    /** @var UserService  */
    private $userService;
    /** @var AccessService  */
    private $accessService;

    public function __construct(TentService $tentService, UserService $userService, AccessService $accessService) {
        $this->tentService = $tentService;
        $this->userService = $userService;
        $this->accessService = $accessService;
    }

    public function indexAction() {
        $managerSites = $this->getConfiguration();
        
        return array(
            'sites' => $managerSites,
        );
    }

    public function tentAction()
    {
        $site = $this->getConfiguration()['tent'];

        $allTents = $this->tentService->getAllTents();
        $allTents = $this->tentService->createTentSetReadables($allTents);
        $dataTableData = $allTents->toArray();
        $dataTable = new DataTable(array(
            'data' => $dataTableData,
            'columns' => array(
                array(
                    'name' => 'readableUser', 'label' => 'Von'
                ),
                array (
                    'name' => 'shapeImg', 'label' => 'Form'
                ),
                array (
                    'name' => 'readableType', 'label' => 'Typ'
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
                    'name' => 'colorField', 'label' => 'Farbe'
                ),
//                array (
//                    'name' => 'biColor', 'label' => 'Zwei-<br/>farbig'
//                ),
//                array (
//                    'name' => 'color2', 'label' => 'Farbe 2'
//                ),
                array (
                    'name' => 'isShowTent', 'label' => 'Schauzelt'
                ),
                array (
                    'name' => 'isGroupEquip', 'label' => 'Gruppen-<br/>eigentum'
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
                            $delete = '<a href="/equip/tent/delete/' . $row['id'] . '">Delete</a>';
                        }
                        return $link1. '<br/>' .$link2. '<br/>' .$edit.'<br/>'.$delete;
                    }
                ),
            )
        ));

        return array(
            'site' => $site,
            'dataTable' => $dataTable
        );

    }
    public function usertentallAction(){
        $userId = (int) $this->params()->fromRoute('userId');
        $allTents = $this->tentService->getTentsByUserId($userId);

        $allTents = $this->tentService->createTentSetReadables($allTents);
        $dataTableData = $allTents->toArray();
        $dataTable = new DataTable(array(
            'data' => $dataTableData,
            'columns' => array(
//                array(
//                    'name' => 'readableUser', 'label' => 'Von'
//                ),
                array (
                    'name' => 'shapeImg', 'label' => 'Form'
                ),
                array (
                    'name' => 'readableType', 'label' => 'Typ'
                ),
                array (
                    'name' => 'width', 'label' => 'Breite'
                ),
                array (
                    'name' => 'length', 'label' => 'Tiefe'
                ),
                array (
                    'name' => 'spareBeds', 'label' => 'freie Schlaf- plätze'
                ),
                array (
                    'name' => 'color1', 'label' => 'Farbe'
                ),
                array (
                    'name' => 'biColor', 'label' => 'Zwei- farbig'
                ),
                array (
                    'name' => 'color2', 'label' => 'Farbe 2'
                ),
                array (
                    'name' => 'isShowTent', 'label' => 'Schauzelt'
                ),
                array (
                    'name' => 'isGroupEquip', 'label' => 'Gruppen- eigentum'
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
                            $delete = '<a href="/equip/tent/delete/' . $row['id'] . '">Delete</a>';
                        }
                        return $link1. '<br/>' .$edit.'<br/>'.$delete;
                    }
                ),
            )
        ));
        return array(
            'dataTable' => $dataTable
        );
    }
    public function usertentAction(){}
    public function deletetentAction(){}
    public function addtentAction()
    {
        $form = new TentForm($this->tentService, $this->userService);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            $form->setData($post);
            if ($form->isValid()){
                $data = new Tent($form->getData());
                $this->tentService->saveTent($data);
            }
        }

    }
    public function edittentAction(){}

    private function getConfiguration()
    {
        return include_once (getcwd(). '\module\Equipment\config\EquipManager.config.php');
    }
}
