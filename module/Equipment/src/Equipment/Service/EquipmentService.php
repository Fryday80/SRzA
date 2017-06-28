<?php

namespace Equipment\Service;


use Application\Model\DataSet;
use Application\Service\CacheService;
use Auth\Service\UserService;
use Equipment\Model\EnumEquipTypes;
use Equipment\Model\EnumTentShape;
use Equipment\Model\EquipTable;
use Equipment\Model\Tent;
use Equipment\Model\TentTypesTable;

class EquipmentService
{
    // tables
    /** @var  TentTypesTable */
    private $typesTable;
    /** @var EquipTable  */
    private $equipTable;

    // services
    /** @var UserService  */
    private $userService;
    /** @var CacheService  */
    private $cache;


    public function __construct (
        TentTypesTable $tentTypesTable, EquipTable $equipTable,
        UserService $userService, CacheService $cacheService )
    {
        $this->typesTable = $tentTypesTable;
        $this->equipTable = $equipTable;
        $this->userService = $userService;
        $this->cache = $cacheService;
    }


    public function deleteAllByUserId($userId)
    {
        return $this->equipTable->removeByUserId($userId);
    }
    
    //======================================================== Tent Table
    public function getAllTents()
    {
        $result = $this->equipTable->getAllByType(EnumEquipTypes::TENT);
        return $this->createTentSetReadables($result);
    }

    public function getTentsByUserId($id)
    {
        return $this->createTentSetReadables($this->equipTable->getByUserIdAndType($id, EnumEquipTypes::TENT));
    }

    public function getTentById($id)
    {
        return $this->createTentReadables($this->equipTable->getById($id));
    }

    public function getCanvasData()
    {
        return $this->equipTable->fetchAllCastData();
    }

    public function saveTent(Tent $tentData)
    {
        if($tentData->id == "") {
            $tentData->itemType = EnumEquipTypes::TENT;
            return $this->equipTable->add($tentData);
        }
        return $this->equipTable->save($tentData);
    }

    public function deleteTentById($id)
    {
        return $this->equipTable->removeById($id);
    }

    public function deleteTentByUserId($userId)
    {
        return $this->equipTable->removeByUserIdAndType($userId, EnumEquipTypes::TENT);
    }

    //======================================================== TentTypes Table
    public function getAllTypes()
    {
        return $this->typesTable->getAll();
    }

    public function getTypeIDTypeNameList()
    {
        $return = array();
        $res = $this->getAllTypes();
        foreach ($res as $item) {
            $return[$item['id']] = $item['name'];
        }
        return $return;
    }

//    public function saveType($data)
//    {
//        if ($data['id'] == "") return $this->typesTable->add($data);
//        return $this->typesTable->save($data);
//    }
//
//    public function deleteType($id)
//    {
//        return $this->typesTable->remove($id);
//    }

    //==========================================================

    private function createTentSetReadables(DataSet $tentSet)
    {
        foreach ($tentSet->data as $key => $tent)
            $tentSet->data[$key] = $this->createTentReadables($tent);
        return $tentSet;
    }

    private function createTentReadables(Tent $tent)
    {
        if ($tent->userId == 0)
            $tent->readableUser = 'Verein';
        else
            $tent->readableUser  = $this->userService->getUserNameByID($tent->userId);
        if ($tent->type == 0)
            $tent->readableType = 'Sonstige';
        else
            $tent->readableType = $this->typesTable->getById($tent->type)['name'];
        $tent->readableShape = EnumTentShape::TRANSLATION[$tent->shape];
        $tent->shapeImg = EnumTentShape::IMAGINATION[$tent->shape];
        $tent->isShowTentValue = ($tent->isShowTent == 0) ? 'nein' : 'ja';
        $c1 = $tent->color1;
        $c2 = $tent->color2;
        $tent->colorField = '<div style="
            width: 0;
            height: 0;
            border-left:   20px solid ' .$c1. ';
            border-top:    20px solid ' .$c1. ';
            border-right:  20px solid ' .$c2. ';
            border-bottom: 20px solid ' .$c2. ';
            "></div>';
        return $tent;
    }
}