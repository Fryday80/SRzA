<?php
	namespace Application\Service;


	use Application\Model\AbstractModels\DatabaseTable;

	class DataService
	{
		/** @var  DatabaseTable */
		protected $table;


		public function getAll()
		{
			return $this->table->getAll();
		}

		public function getById($id)
		{
			return $this->table->getById($id);
		}

		public function deleteById($id)
		{
			return $this->table->remove($id);
		}

		public function getNextId()
		{
			return $this->table->getNextId();
		}
	}