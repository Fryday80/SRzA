<?php

namespace Equipment\Service;


use Application\Model\DataSet;
use Application\Service\CacheService;
use Auth\Service\UserService;
use Equipment\Model\EnumEquipTypes;
use Equipment\Model\EnumTentShape;
use Equipment\Model\EquipTable;
use Equipment\Model\Tent;
use Equipment\Model\TentSet;
use Equipment\Model\TentTable;
use Equipment\Model\TentTypesTable;

class TentService
{
    // tables
    /** @var  TentTable */
    private $tentTable;
    /** @var  TentTypesTable */
    private $typesTable;

    // services
    /** @var UserService  */
    private $userService;
    /** @var CacheService  */
    private $cache;
    /** @var EquipTable  */
    private $equipTable;


    public function __construct (
        TentTable $tentTable, TentTypesTable $tentTypesTable, EquipTable $equipTable,
        UserService $userService, CacheService $cacheService )
    {
        $this->tentTable = $tentTable;
        $this->typesTable = $tentTypesTable;
        $this->equipTable = $equipTable;
        $this->userService = $userService;
        $this->cache = $cacheService;
    }
    
    //======================================================== Tent Table
    public function getAllTents()
    {
        $res = $this->equipTable->getAllByType(EnumEquipTypes::TENT);
        bdump($res);
        return $this->createTentSetReadables($this->tentTable->getAll());
    }

    public function getTentsByUserId($id)
    {
        return $this->createTentSetReadables($this->tentTable->getByUserId($id));
    }

    public function getTentById($id)
    {
        return $this->createTentReadables($this->tentTable->getById($id));
    }

    public function getCanvasData()
    {
        return $this->tentTable->fetchAllCastData();
    }

    public function saveTent(Tent $tentData)
    {
//        if (!$this->equipTable->getById($tentData->id))
//            $this->equipTable->add($tentData, EnumEquipTypes::TENT);
        return $tentData->id =  $this->tentTable->save($tentData);
    }

    public function deleteTentById($id)
    {
        return $this->tentTable->removeById($id);
    }

    public function deleteTentByUserId($userId)
    {
        return $this->tentTable->removeByUserId($userId);
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

    public function saveType($data)
    {
        if ($data['id'] == "") return $this->typesTable->add($data);
        return $this->typesTable->save($data);
    }

    public function deleteType($id)
    {
        return $this->typesTable->remove($id);
    }

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