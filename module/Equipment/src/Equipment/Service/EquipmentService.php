<?php

namespace Equipment\Service;


use Application\Model\DataSet;
use Application\Service\CacheService;
use Auth\Service\UserService;
use Equipment\Model\EEquipTypes;
use Equipment\Model\ETentShape;
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
    /** @var CacheService  */
    private $cache;


    public function __construct (
        EquipTable $equipTable, CacheService $cacheService )
    {
        $this->equipTable = $equipTable;
        $this->cache = $cacheService;
    }


    /**
     * @param int $type use EEquipTypes::
     * @return DataSet|bool
     */
    public function getAllByType($type)
    {
        return $this->equipTable->getAllByType($type);
    }

    /**
     * @param int $userId
     * @param int $type use EEquipTypes::
     * @return DataSet|bool
     */
    public function getByUserIdAndType($userId, $type)
    {
        return $this->equipTable->getByUserIdAndType($userId, $type);
    }

    public function getById($id)
    {
        return $this->equipTable->getById($id);
    }

    public function deleteAllByUserId($userId)
    {
        return $this->equipTable->removeByUserId($userId);
    }
    public function deleteById($id)
    {
        return $this->equipTable->removeById($id);
    }
    //======================================================== Tent Table
    public function getAllTents()
    {
        return $this->equipTable->getAllByType(EEquipTypes::TENT);
    }

    public function getTentsByUserId($id)
    {
        return $this->equipTable->getByUserIdAndType($id, EEquipTypes::TENT);
    }

    public function getTentById($id)
    {
        return $this->equipTable->getById($id);
    }

    public function getCanvasData()
    {
        return $this->equipTable->fetchAllCastData();
    }

    public function save($data)
    {
        if ($data instanceof Tent) $this->saveTent($data);
        if($data->id == "")
            return $this->equipTable->add($data);
        return $this->equipTable->save($data);
    }


    public function saveTent(Tent $tentData)
    {
        $tentData->image = ETentShape::IMAGES[$tentData->shape];
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
        return $this->equipTable->removeByUserIdAndType($userId, EEquipTypes::TENT);
    }

    /**
     * @param EEquipTypes $type
     * @return array|false
     * @throws \Exception
     */
    public function getUserList($type = null)
    {
        return $this->equipTable->getUserList($type);
    }
}