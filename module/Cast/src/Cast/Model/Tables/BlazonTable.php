<?php
namespace Cast\Model\Tables;

use Application\Hydrator\HydratingResultSet;
use Application\Model\AbstractModels\DatabaseTable;
use Cast\Model\DataModels\Blazon;
use Zend\Db\Adapter\Adapter;

class BlazonTable extends DatabaseTable
{
	public $table = 'blazon';

	public function __construct(Adapter $adapter)
	{
		parent::__construct($adapter, Blazon::class);
	}

    public function getAllOverlays () {
        $row = $this->select(array('is_overlay' => 1));
        if (!$row)
            return false;

        return $row->toArray();
    }

    public function getAllNotOverlay () {
        $row = $this->select(array('is_overlay' => 0));
        if (!$row)
            return false;

        return $row->toArray();
    }
}
