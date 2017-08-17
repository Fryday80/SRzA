<?php
namespace Equipment\Service;

use Application\Service\CacheService;
use Equipment\Hydrator\EquipmentResultSet;
use Equipment\Model\Enums\EEquipTypes;
use Equipment\Model\Enums\ETentShape;
use Equipment\Model\Tables\lostAndFoundTable;

class LostAndFoundService
{
    // tables
    /** @var LostAndFoundTable  */
    private $lostAndFoundTable;

    // services
    /** @var CacheService  */
    private $cache;


    public function __construct (
        LostAndFoundTable $lostAndFoundTable, CacheService $cacheService )
    {
        $this->lostAndFoundTable = $lostAndFoundTable;
        $this->cache = $cacheService;
    }

    public function getAll()
    {
        return $this->lostAndFoundTable->getAll();
    }

    public function getById($id)
    {
        return $this->lostAndFoundTable->getById($id);
    }

    public function deleteById($id)
    {
        return $this->lostAndFoundTable->removeById($id);
    }


	public function getNextId()
	{
		return $this->lostAndFoundTable->getNextId();
	}

    public function save($data)
    {
        if($data->id == "")
            return $this->lostAndFoundTable->add($data);

        $item = $this->getById($data->id);
        if ($data instanceof Tent)
            $data->image = ETentShape::IMAGES[$data->shape];
        return $this->lostAndFoundTable->save($data);
    }
}