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
    private $table;

    // services
    /** @var CacheService  */
    private $cache;


    public function __construct (
        EquipTable $equipTable, CacheService $cacheService )
    {
        $this->table = $equipTable;
        $this->cache = $cacheService;
    }

    public function getAll()
    {
        return $this->table->getAll();
    }

    public function getAllPlannerObjects(){
    	return $this->table->getAllPlannerObjects();
	}

    /**
     * @param int $type use EnumEquipTypes::
     * @return EquipmentResultSet|bool
     */
    public function getAllByType($type)
    {
        return $this->table->getAllByType($type);
    }

    /**
     * @param int $userId
     * @param int $type use EEquipTypes::
     * @return EquipmentResultSet|bool
     */
    public function getByUserIdAndType($userId, $type)
    {
        return $this->table->getByUserIdAndType($userId, $type);
    }

    public function getById($id)
    {
        return $this->table->getById($id);
    }

    public function deleteAllByUserId($userId)
    {
        return $this->table->removeByUserId($userId);
    }

    /**
     * @param EEquipTypes $type
     * @param int $userId
     */
    public function deleteByTypeAndUSerId($type, $userId)
    {
        $this->table->removeByUserIdAndType((int)$userId, $type);
    }

    public function deleteById($id)
    {
        return $this->table->remove($id);
    }


	public function getNextId()
	{
		return $this->table->getNextId();
	}

    public function save($data)
    {
    	$id = (is_object($data)) ? $data->id : $data['id'];
        if($id == "")
            return $this->table->add($data);

        $item = $this->getById($id);
        if ($data instanceof Tent)
            $data->image = ETentShape::IMAGES[$data->shape];
        else
			$data['image']= ETentShape::IMAGES[$data['shape']];
        return $this->table->save($data);
    }
}