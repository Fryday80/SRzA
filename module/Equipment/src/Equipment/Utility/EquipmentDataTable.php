<?php
namespace Equipment\Utility;


use Application\Utility\DataTable;
use Auth\Service\AccessService;
use Equipment\Model\EEquipTypes;
use Equipment\Model\ETentShape;
use Equipment\Model\ETentType;
use Equipment\Service\EquipmentService;

class EquipmentDataTable extends DataTable
{
    public $items;
    // check vars
    private $aService = false;
    private $eService = false;
    private $tablePrepared = false;

    // Services
    /** @var  AccessService */
    private $accessService;
    /** @var  EquipmentService */
    private $equipService;

    public function __construct($items = null)
    {
        $this->items = $items;
    }

    public function setAccessService(AccessService $accessService)
    {
        $this->accessService = $accessService;
        $this->aService = true;
    }

    public function setEquipmentService(EquipmentService $equipService)
    {
        $this->equipService = $equipService;
        $this->eService = true;
    }

    public function setServices (AccessService $accessService, EquipmentService $equipService){
        $this->setAccessService($accessService);
        $this->setEquipmentService($equipService);
    }

    public function configure($action, $type, $items = null)
    {
        parent::__construct(array(
            'data' => ($items !== null)? $items : $this->items,
            'columns' => $this->$action($type)
        ));
        $typeString = EEquipTypes::TRANSLATE_TO_STRING[$type];
        $this->insertLinkButton("/equip/$typeString/add", 'Neuer Eintrag');
    }

    public function isPrepared()
    {
        if ($this->aService == true && $this->eService == true){
            $this->tablePrepared = true;
            return $this->tablePrepared;
        }
        else {
            bdump ('You need to inject AccessService and EquipmentService!! Use: setAccessService && setEquipmentService');
            return false;
        }
    }

    private function type($type){
        if (!$this->isPrepared()) return false;
        $dataTableVarColumns = array();
        $dataTableVarColumns[] = array(
            'name' => 'userName', 'label' => 'Von',
        );
        return array_merge_recursive($dataTableVarColumns, $this->userall($type));
    }
    private function userall($type){
        if (!$this->isPrepared()) return false;
        if($type == EEquipTypes::TENT)
        return array(
            array(
                'name' => 'name', 'label' => 'Name'
            ),
            array(
                'name' => 'image', 'label' => 'Form', 'type' => 'custom',
                'render' => function ($row) {
                    return '<img alt="' . ETentShape::TRANSLATION[$row['shape']] . '" src="' . $row['image'] . '" style="width: 50px">';
                }
            ),
            array(
                'name' => 'readableType', 'label' => 'Typ', 'type' => 'custom',
                'render' => function ($row) {
                    return ETentType::TRANSLATE_TO_STRING[$row['type']];
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

        if($type == EEquipTypes::EQUIPMENT)
            return array(
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
        return null;
    }
}