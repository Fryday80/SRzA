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
        return $this->equipTable->getAllByType(EnumEquipTypes::TENT);
    }

    public function getTentsByUserId($id)
    {
        return $this->equipTable->getByUserIdAndType($id, EnumEquipTypes::TENT);
    }

    public function getTentById($id)
    {
        return $this->equipTable->getById($id);
    }

    public function getCanvasData()
    {
        return $this->equipTable->fetchAllCastData();
    }

    public function saveTent(Tent $tentData)
    {
        $tentData->image = EnumTentShape::IMAGES[$tentData->shape];
        if($tentData->id == "")
            return $this->equipTable->add($tentData);
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

    public function getTypeNameById($id)
    {
        return $this->typesTable->getById($id)['name'];
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
}