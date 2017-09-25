<?php
	namespace Cast\Model\DataModels;


	use Application\Model\AbstractModels\AbstractModel;

	class Blazon extends AbstractModel
	{
		public $id;
		public $name = null;
		public $filename = null;
		public $bigFilename = null;
		public $isOverlay = 0;

		public function appendDataArray($data)
		{
			foreach ($data as $key => $value) {
				if ($key == 'id') $value = (int)$value;
				$this->$key = $value;
			}
		}

		public function setId($id)
		{
			$this->id = (int) $id;
		}
	}