<?php
	/**
	 * Created by IntelliJ IDEA.
	 * User: Fry
	 * Date: 22.07.2017
	 * Time: 12:07
	 */

	namespace Application\Utility;


	use Application\Model\Tables\SystemLogTable;

	class SysLogBuffer extends CircularBuffer
	{
		private $dbCluster;
		private $entries = 0;

		private $firstInit = true;
		/** @var  SystemLogTable */
		private $table;

		/**
		 * SysLogBuffer constructor.
		 *
		 * @param     $table
		 * @param     $size
		 * @param int $dbCluster [optional] sets limit when buffer is saved to db, -1 for direct save, 0 | nothing sets 50%
		 */
		public function __construct($table, $size, $dbCluster = 0)
		{
			parent::__construct($size);
			$this->table = $table;
			if ($dbCluster == 0) $dbCluster = $size/2;
			$this->dbCluster = $dbCluster;
		}

		public function push($value)
		{
			$this->entries++;
			parent::push($value);
			$this->afterPush($value);
		}

		public function afterPush($value)
		{
			if ($this->dbCluster < 0){
				/*
				 * direct save
				 */
				$this->table->updateSystemLog($value);
			} else {
				if ($this->entries == $this->DataSize){
					$data = $this->toArray(); //@todo check if array reverse
					if ($this->firstInit){
						/*
						 * save whole buffer data
						 */
						$this->table->saveMultiple($data);
						$this->firstInit = false;
					} else {
						/*
						 * if limit is reached
						 * save newer half of the buffer
						 */
						$data = array_slice($data,$this->dbCluster);
						$this->table->saveMultiple($data);
					}
				}
			}
		}
	}