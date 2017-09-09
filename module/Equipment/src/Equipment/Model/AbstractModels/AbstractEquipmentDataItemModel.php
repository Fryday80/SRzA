<?php
namespace Equipment\Model\AbstractModels;

use Application\Model\AbstractModels\AbstractModel;

class AbstractEquipmentDataItemModel extends AbstractModel
{
    /** @var  int */
    public $id;
    /** @var  int */
    public $itemType;
    /** @var  string path to img, used in SitePlanner*/
    public $image;
    /** @var  int */
    public $userId;
    /** @var  string */
    public $userName;
    /** @var  int used as bool */
    public $sitePlannerObject;
    /** @var  int depth in cm used to render in SitePlanner */
    public $depth;
    /** @var  int width in cm used to render in SitePlanner */
    public $width;
    public $shape;
    public $name;
    public $description;
    public $image1;
    public $image2;
    public $color1;
    public $biColor;
    public $color2;
    public $bill;
    public $purchased;
    public $amount;
    public $stored;
    public $lending;

    private $metaData = array (
    	'id' 		=> 'id',
    	'image' 	=> 'image',
    	'userId' 	=> 'user_id',
    	'userName' 	=> 'user_name',
		'itemType' 	=> 'item_type',
		'sitePlannerObject' => 'site_planner_object',
	);

    public function __construct($data = null)
    {
        if ($data !== null){
            foreach ($data as $key=>$value)
                $this->$key = $value;
        }
    }

    // move to EquipmentResultSet ??
    public function metaDataUpdate($data)
    {
    	foreach ($this->metaData as $cCase => $sub_str)
    	{
    		if ($cCase == 'id') $this->$cCase = $data[$sub_str];
    		else $this->$cCase = ($data[$sub_str] !== null) ? $data[$sub_str] : $this->$cCase;
		}
    }

	/**
	 * is this group equip
	 * @return bool
	 */
	public function isGroupEquip()
	{
		if ($this->userId == 0) return true;
		return false;
	}
}