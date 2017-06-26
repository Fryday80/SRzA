<?php
namespace Equipment\Controller;

use Application\Utility\DataTable;
use Auth\Service\AccessService;
use Auth\Service\UserService;
use Equipment\Form\TentColorsForm;
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
        $test = 'empty';
        $dataTable = 'empty';
        $form = new Tent();

        $allTents = $this->tentService->getAllTents();
        $allTents = $this->tentService->makeTentSetReadables($allTents);
        $dataTableData = $allTents->toArray();
        $dataTable = new DataTable(array(
            'data' => $dataTableData,
            'columns' => array(
                array(
                    'name' => 'readableUser', 'label' => 'Von'
                    ),
                array (
                    'name' => 'readableShape', 'label' => 'Form'
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
                    'name' => 'spareBeds', 'label' => 'freie Schlafplätze'
                    ),
                array (
                    'name' => 'color1', 'label' => 'Farbe'
                    ),
                array (
                    'name' => 'biColor', 'label' => 'Zweifarbig'
                    ),
                array (
                    'name' => 'color2', 'label' => 'Farbe 2'
                    ),
                array (
                    'name' => 'isShowTent', 'label' => 'Schauzelt'
                    ),
                array (
                    'name' => 'isGroupEquip', 'label' => 'Gruppeneigentum'
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
                        if ( $row['userId'] == $askingId || $askingRole == 'Administrator' ) {
                            $edit   = '<a href="eqip/tent/edit/' . $row['id'] . '">Edit</a>';
                            $delete = '<a href="eqip/tent/delete/' . $row['id'] . '">Delete</a>';
                        }
                        return $edit.' '.$delete;
                    }
                ),
            )
        ));
        
        return array(
            'form' => $form,
            'test' => $test,
            'dataTable' => $dataTable
        );
    }

    public function addAction()
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

}
