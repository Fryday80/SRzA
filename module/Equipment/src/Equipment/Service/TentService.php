<?php

namespace Equipment\Service;


use Application\Service\CacheService;
use Auth\Service\UserService;
use Equipment\Model\EnumTentShape;
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


    public function __construct (
        TentTable $tentTable, TentTypesTable $tentTypesTable,
        UserService $userService, CacheService $cacheService )
    {
        $this->tentTable = $tentTable;
        $this->typesTable = $tentTypesTable;
        $this->userService = $userService;
        $this->cache = $cacheService;
    }
    //==========================================================

    public function createTentSetReadables(TentSet $tentSet)
    {
        foreach ($tentSet->data as $key => $tent)
            $tentSet->data[$key] = $this->createTentReadables($tent);
        return $tentSet;
    }
    
    public function createTentReadables(Tent $tent)
    {
        $tent->readableUser  = $this->userService->getUserNameByID($tent->userId);
        $tent->readableShape = EnumTentShape::TRANSLATION[$tent->shape];
        $tent->shapeImg = EnumTentShape::IMAGINATION[$tent->shape];
        $tent->readableType  = $this->typesTable->getById($tent->type)['name'];
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
    
    //======================================================== Tent Table
    public function getAllTents()
    {
        return $this->tentTable->getAll();
    }

    public function getTentsByUserId($id)
    {
        return $this->tentTable->getByUserId($id);
    }

    public function getTentById($id)
    {
        return $this->tentTable->getById($id);
    }

    public function getCanvasData()
    {
        return $this->tentTable->fetchAllCastData();
    }

    public function saveTent(Tent $tentData)
    {
        $tentData->id =  $this->tentTable->save($tentData);
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
}