<?php

namespace Equipment\Service;


use Application\Model\DataSet;
use Application\Service\CacheService;
use Equipment\Model\EEquipTypes;
use Equipment\Model\ETentShape;
use Equipment\Model\EquipTable;
use Equipment\Model\Tent;

class EquipmentService
{
    // tables
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


    public function getAll()
    {
        return $this->equipTable->getAll();
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

    /**
     * @param EEquipTypes $type
     * @param int $userId
     */
    public function deleteByTypeAndUSerId($type, $userId)
    {
        $this->equipTable->removeByUserIdAndType((int)$userId, $type);
    }

    public function deleteById($id)
    {
        return $this->equipTable->removeById($id);
    }

    public function save($data)
    {
        if ($data instanceof Tent)
            $data->image = ETentShape::IMAGES[$data->shape];
        if($data->id == "")
            return $this->equipTable->add($data);
        return $this->equipTable->save($data);
    }
    // DEPRECATED!!!!!!!!

    /**
     * DEPRECATED +++ DEPRECATED +++ replaced by getAllByType
     * @return DataSet|bool
     */
    public function getAllTents()
    {
        $this->depMsg();
        return $this->getAllByType(EEquipTypes::TENT);
    }

    /**
     * DEPRECATED +++ DEPRECATED +++ replaced by getByUserIdAndType
     * @param $id
     * @return DataSet|bool
     */
    public function getTentsByUserId($id)
    {
        $this->$this->depMsg();
        return $this->getByUserIdAndType($id, EEquipTypes::TENT);
    }

    /**
     * DEPRECATED +++ DEPRECATED +++ replaced by getById
     * @param $id
     * @return \Application\Model\DataObjects\DataItem|bool
     */
    public function getTentById($id)
    {
        $this->$this->depMsg();
        return $this->equipTable->getById($id);
    }

    /**
     * DEPRECATED +++ DEPRECATED +++ replaced by getAll
     * @return array
     */
    public function getCanvasData()
    {
        $this->$this->depMsg();
        return $this->equipTable->fetchAllCastData();
    }

    /**
     * DEPRECATED +++ DEPRECATED +++ replaced by save
     * @param Tent $tentData
     * @return bool|int
     */
    public function saveTent(Tent $tentData)
    {
        $this->$this->depMsg();
        $tentData->image = ETentShape::IMAGES[$tentData->shape];
        if($tentData->id == "")
            return $this->equipTable->add($tentData);
        return $this->equipTable->save($tentData);
    }

    /**
     * DEPRECATED +++ DEPRECATED +++ replaced by delteById
     * @param $id
     * @return bool
     */
    public function deleteTentById($id)
    {
        $this->$this->depMsg();
        return $this->deleteById($id);
    }

    /**
     * DEPRECATED +++ DEPRECATED +++ replaced by deleteByTypeAndUSerId
     * @param $userId
     * @return bool
     */
    public function deleteTentByUserId($userId)
    {
        $this->$this->depMsg();
        return $this->equipTable->removeByUserIdAndType($userId, EEquipTypes::TENT);
    }

    private function depMsg()
    {
        bdump('DEPRECATED METHOD USED!!!');
    }
}