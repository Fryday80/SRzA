<?php
namespace Equipment\Service;

use Application\Service\CacheService;
use Application\Service\MyService;
use Equipment\Hydrator\EquipmentResultSet;
use Equipment\Model\DataModels\LostAndFoundItem;
use Equipment\Model\Enums\EEquipTypes;
use Equipment\Model\Enums\ETentShape;
use Equipment\Model\Tables\lostAndFoundTable;

class LostAndFoundService extends MyService
{
    // tables
    /** @var LostAndFoundTable  */
    protected $table;

    // services
    /** @var CacheService  */
    private $cache;


    public function __construct (
        LostAndFoundTable $lostAndFoundTable, CacheService $cacheService )
    {
        $this->table = $lostAndFoundTable;
        $this->cache = $cacheService;
    }

    public function getAll()
    {
        return $this->table->getAll();
    }

    public function getById($id)
    {
        return $this->table->getById($id);
    }

	public function save($data)
	{
		if($data['id'] == "" || $data['id'] == null)
			return $this->table->add($data);
		else {
			return $this->table->save($data);
		}
	}

    public function deleteById($id)
    {
        return $this->table->removeById($id);
    }

	public function getNextId()
	{
		return $this->table->getNextId();
	}
}