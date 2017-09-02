<?php
namespace Equipment\Service;

use Application\Service\CacheService;
use Equipment\Hydrator\EquipmentResultSet;
use Equipment\Model\DataModels\Tent;
use Equipment\Model\Enums\EEquipTypes;
use Equipment\Model\Enums\ETentShape;
use Equipment\Model\EquipTable;

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

    public function getAllPlannerObjects(){
    	return $this->equipTable->getAllPlannerObjects();
	}

    /**
     * @param int $type use EnumEquipTypes::
     * @return EquipmentResultSet|bool
     */
    public function getAllByType($type)
    {
        return $this->equipTable->getAllByType($type);
    }

    /**
     * @param int $userId
     * @param int $type use EEquipTypes::
     * @return EquipmentResultSet|bool
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


	public function getNextId()
	{
		return $this->equipTable->getNextId();
	}

    public function save($data)
    {
    	$id = (is_object($data)) ? $data->id : $data['id'];
        if($id == "")
            return $this->equipTable->add($data);

        $item = $this->getById($id);
        if ($data instanceof Tent)
            $data->image = ETentShape::IMAGES[$data->shape];
        else
			$data['image']= ETentShape::IMAGES[$data['shape']];
        return $this->equipTable->save($data);
    }
}