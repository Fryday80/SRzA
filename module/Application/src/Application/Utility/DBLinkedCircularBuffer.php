<?php
	/**
	 * Created by IntelliJ IDEA.
	 * User: Fry
	 * Date: 22.07.2017
	 * Time: 12:07
	 */

	namespace Application\Utility;


	use Application\Model\Tables\SystemLogTable;

	class DBLinkedCircularBuffer extends CircularBuffer
	{
		private $dbCluster;
		private $entries = 0;

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

		/**
		 * @param mixed $value last storage item if db call on each run
		 */
		protected function afterPush($value)
		{
			if ($this->dbCluster < 0){
				/*
				 * direct save
				 */
				$this->table->updateSystemLog($value);
			} else {
				if ($this->entries == $this->dbCluster){
					$data = $this->toArray();
					//@todo check if array reverse
					//@todo check if rearrange of array is needed for slice
					/*
					 * if limit is reached
					 * save newer half of the buffer
					 */
					$data = array_slice($data,$this->dbCluster);
					$this->table->saveMultiple($data);
					$this->entries = 0;
//					}
				}
			}
		}
	}