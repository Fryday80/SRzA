<?php

namespace Equipment\Service;


use Application\Service\CacheService;
use Equipment\Model\Enums\EEquipTypes;
use Equipment\Model\Enums\ETentShape;
use Equipment\Model\Tables\EquipTable;
use Application\Model\AbstractModels\DataSet;
use Equipment\Model\Tent;
use Equipment\Model\DataModels\Equip;

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
     * @param int $type use EnumEquipTypes::
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
        if($data->id == "")
            return $this->equipTable->add($data);

        $item = $this->getById($data->id);
        if ($data instanceof Tent)
            $data->image = ETentShape::IMAGES[$data->shape];
        if ($data instanceof Equipment){
//            if ($data->sitePlannerObject == '1') {
//                if ($data->sitePlannerImage !== NULL) {
//                    $data->image = ($data->sitePlannerImage == 0)
//                        ? EEquipSitePlannerImage::IMAGE_TYPE[$data->sitePlannerImage]
//                        : self::EQUIPMENT_IMAGES_PATH . "$data->userId/" . EEquipSitePlannerImage::IMAGE_TYPE[$data->sitePlannerImage];
//                } else {
//                    $data->sitePlannerImage = 0;
//                    $data->image = EEquipSitePlannerImage::IMAGE_TYPE[$data->sitePlannerImage];
//                }
//            }
        }
        return $this->equipTable->save($data);
    }



    // DEPRECATED!!!!!!!!
    //@todo cleanfix

////  0 usages found 16.7.
//    /**
//     * DEPRECATED +++ DEPRECATED +++ replaced by getAllByType
//     * @return DataSet|bool
//     */
//    public function getAllTents()
//    {
//        $this->depMsg();
//        return $this->getAllByType(EEquipTypes::TENT);
//    }
//
////  0 usages found 16.7.
//    /**
//     * DEPRECATED +++ DEPRECATED +++ replaced by getByUserIdAndType
//     * @param $id
//     * @return DataSet|bool
//     */
//    public function getTentsByUserId($id)
//    {
//        $this->$this->depMsg();
//        return $this->getByUserIdAndType($id, EEquipTypes::TENT);
//    }
//
////  0 usages found 16.7.
//    /**
//     * DEPRECATED +++ DEPRECATED +++ replaced by getById
//     * @param $id
//     * @return \Application\Model\DataModels\DataItem|bool
//     */
//    public function getTentById($id)
//    {
//        $this->$this->depMsg();
//        return $this->equipTable->getById($id);
//    }
//
//  0 usages found 16.7.
//    /**
//     * DEPRECATED +++ DEPRECATED +++ replaced by getAll
//     * @return array
//     */
//    public function getCanvasData()
//    {
//        $this->$this->depMsg();
//        return $this->equipTable->fetchAllCastData();
//    }


//  0 usages found 16.7.
//    /**
//     * DEPRECATED +++ DEPRECATED +++ replaced by save
//     * @param Tent $tentData
//     * @return bool|int
//     */
//    public function saveTent(Tent $tentData)
//    {
//        $this->$this->depMsg();
//        $tentData->image = ETentShape::IMAGES[$tentData->shape];
//        if($tentData->id == "")
//            return $this->equipTable->add($tentData);
//        return $this->equipTable->save($tentData);
//    }


//  0 usages found 16.7.
//    /**
//     * DEPRECATED +++ DEPRECATED +++ replaced by delteById
//     * @param $id
//     * @return bool
//     */
//    public function deleteTentById($id)
//    {
//        $this->$this->depMsg();
//        return $this->deleteById($id);
//    }


//  0 usages found 16.7.
//    /**
//     * DEPRECATED +++ DEPRECATED +++ replaced by deleteByTypeAndUSerId
//     * @param $userId
//     * @return bool
//     */
//    public function deleteTentByUserId($userId)
//    {
//        $this->$this->depMsg();
//        return $this->equipTable->removeByUserIdAndType($userId, EEquipTypes::TENT);
//    }
//
//    private function depMsg()
//    {
//        bdump('DEPRECATED METHOD USED!!!');
//    }
}