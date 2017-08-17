<?php
namespace Equipment\Model\DataModels;


use Application\Model\AbstractModels\AbstractModel;

class LostAndFoundItem extends AbstractModel
{
	/** @var  int */
    public $id;
    /** @var  string */
    public $name;
    /** @var  int user ID */
    public $possessed;
    /** @var  string */
    public $userName;
    /** @var array possible owners */
    public $mightBe;
    /** @var  string path_to_image */
    public $image;
    /** @var  string */
    public $event;
    /** @var  int as bool */
	public $lost = 0;

	public function __construct($data = null)
	{
		if ($data !== null){
			foreach ($data as $key=>$value)
				$this->$key = $value;
		}
		// @todo move to hydrating functions!!
		if (! is_array($this->mightBe)){
			$this->mightBe = unserialize($this->mightBe);
		}
	}

	// @todo (un-) serialize $this->mightBe array
	public function preHydrate($data) { }
	public function preExtract(&$arrayData) { }
}