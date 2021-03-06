<?php
	/**
	 * Created by IntelliJ IDEA.
	 * User: Fry
	 * Date: 24.08.2017
	 * Time: 15:03
	 */

	namespace Application\Utility;


	use module\Application\src\Application\Utility\IClassLog;

	class MyUtility implements IClassLog
	{
		// IClassLog
		protected $classLog;
		protected $logHash;


		/* ========================
		 * IClassLog
		 * ========================*/

		public function getLog()
		{
			return $this->classLog;
		}

		public function log($method, $msg = null)
		{
			if ($msg == null)
				$msg = (key_exists($method, $this->logHash)) ? 'done' : 'start';

			$count = count($this->classLog);
			$this->classLog[$count] = array(
				'method' => $method,
				'msg'	 => $msg,
			);
			$this->logHash[$method][$count] = $msg;
		}
	}