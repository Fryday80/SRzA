<?php
namespace Equipment\Utility;

use Application\Utility\DataTableAbstract;
use Auth\Service\AccessService;
use Equipment\Model\Enums\EEquipSitePlannerImage;
use Equipment\Model\Enums\EEquipTypes;
use Equipment\Model\Enums\ETentShape;
use Equipment\Model\Enums\ETentType;
use Equipment\Service\EquipmentService;

class EquipmentDataTable extends DataTableAbstract
{
    // check vars
    private $aService = false;
    private $eService = false;
    private $tablePrepared = false;

    // Services
    /** @var  AccessService */
    private $accessService;
    /** @var  EquipmentService */
    private $equipService;

    public function setServices (AccessService $accessService, EquipmentService $equipService)
    {
        $this->setAccessService($accessService);
        $this->setEquipmentService($equipService);
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

    public function configure($action, $type = null, $items = null)
    {
        if ($items !== null) parent::setData($items);
        parent::setColumns($this->$action($type));

        if ($type !== null) {
            $typeString = EEquipTypes::TRANSLATE_TO_STRING[$type];
            $this->insertLinkButton("/equip/$typeString/add", 'Neuer Eintrag');
        }
        else {
            foreach (EEquipTypes::TRANSLATE_TO_STRING as $item) {
                $this->insertLinkButton("/equip/$item/add", "Neuer '$item item' Eintrag");
            }
        }
    }

    public function isPrepared()
    {
        if ($this->aService == true && $this->eService == true)
            return $this->tablePrepared = true;
        else
            bdump ('You need to inject AccessService and EquipmentService!! Use: setAccessService && setEquipmentService');
        return false;
    }

    private function index(){
        return array(
            array (
                'name'  => 'name',
                'label' => 'Name'
            ),
            array (
                'name'  => 'typ',
                'label' => 'Typ',
                'type'  => 'custom',
                'render' => function($row) {
                    return EEquipTypes::TRANSLATE_TO_STRING[$row['itemType']];
                }
            ),
            array (
                'name'  => 'userName',
                'label' => 'Besitzer'
            ),
            array (
                'name'  => 'image',
                'label' => 'Bild',
                'type'  => 'custom',
                'render' => function($row) {
                    if ((int)$row['sitePlannerObject'] == 1) {
                        if (isset($row['image']) && $row['image'] !== EEquipSitePlannerImage::IMAGE_TYPE[EEquipSitePlannerImage::DRAW])
                            return '<img src="' . $row['image'] . '" alt="Item" style="height:35px;">';
                        if (isset($row['sitePlannerImage']) && (int)$row['sitePlannerImage'] == EEquipSitePlannerImage::DRAW) {
                            if (isset($row['color1'])) {
                                $c1 = $row['color1'];
                                $c2 = $row['color2'];
                            } else {
                                $c1 = $c2 = $row['color'];
                            }
                            return '<div style="
                                    width: 0;
                                    height: 0;
                                    border-left:   20px solid ' . $c1 . ';
                                    border-top:    20px solid ' . $c1 . ';
                                    border-right:  20px solid ' . $c2 . ';
                                    border-bottom: 20px solid ' . $c2 . ';
                                    "></div>';
                        } else {
                            return '<img src="' . $row['image'] . '.png" alt="Item" style="height:35px;">';
                        }
                    }
                    return '';
                }
            ),
            array (
                'name'  => 'href',
                'label' => 'Details',
                'type'  => 'custom',
                'render' => function($row) {
                    $edit = '';
                    $delete = '';
                    $askingId = $this->accessService->getUserID();
                    $askingRole = $this->accessService->getRole();
                    $link1 = '<a href="/equip/' . EEquipTypes::TRANSLATE_TO_STRING[$row['itemType']] . '/' . $row['userId'] . '/show/' . $row['id'] . '">Details</a>';
                    if ($row['userId'] == $askingId || $askingRole == 'Administrator') {
                        $edit = '<a href="/equip/' . EEquipTypes::TRANSLATE_TO_STRING[$row['itemType']] . '/' . $row['userId'] . '/edit/' . $row['id'] . '">Edit</a>';
                        $delete = '<a href="/equip/' . EEquipTypes::TRANSLATE_TO_STRING[$row['itemType']] . '/' . $row['userId'] . '/delete/' . $row['id'] . '">Delete</a>';
                    }
                    return $link1 . '<br/>' . $edit . '<br/>' . $delete;
                }
            ),
        );
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
                    $link1 = '<a href="/equip/' . EEquipTypes::TRANSLATE_TO_STRING[$row['itemType']] . '/' . $row['userId'] . '/show/' . $row['id'] . '">Details</a>';
                    if ($row['userId'] == $askingId || $askingRole == 'Administrator') {
                        $edit = '<a href="/equip/' . EEquipTypes::TRANSLATE_TO_STRING[$row['itemType']] . '/' . $row['userId'] . '/edit/' . $row['id'] . '">Edit</a>';
                        $delete = '<a href="/equip/' . EEquipTypes::TRANSLATE_TO_STRING[$row['itemType']] . '/' . $row['userId'] . '/delete/' . $row['id'] . '">Delete</a>';
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
                        return ($row['type'] == 0) ? 'Sonstige' : EEquipTypes::TRANSLATE_TO_STRING[$row['type']];
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
                        bdump($row);
                        if ((int)$row['sitePlannerImage'] == EEquipSitePlannerImage::DRAW) {
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
                        } else {
                            return '<img src="' . $row['image'] . '" alt="Item" style="height:35px;">';
                        }
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
                        $link1 = '<a href="/equip/' . EEquipTypes::TRANSLATE_TO_STRING[$row['itemType']] . '/' . $row['userId'] . '/show/' . $row['id'] . '">Details</a>';
                        if ($row['userId'] == $askingId || $askingRole == 'Administrator') {
                            $edit = '<a href="/equip/' . EEquipTypes::TRANSLATE_TO_STRING[$row['itemType']] . '/' . $row['userId'] . '/edit/' . $row['id'] . '">Edit</a>';
                            $delete = '<a href="/equip/' . EEquipTypes::TRANSLATE_TO_STRING[$row['itemType']] . '/' . $row['userId'] . '/delete/' . $row['id'] . '">Delete</a>';
                        }
                        return $link1 . '<br/>' . $edit . '<br/>' . $delete;
                    }
                ),
            );
        return null;
    }
}