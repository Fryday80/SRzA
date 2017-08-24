<?php
	/**
	 * Created by IntelliJ IDEA.
	 * User: Fry
	 * Date: 24.08.2017
	 * Time: 15:01
	 */

	namespace Application\Controller\Plugin;

	use module\Application\src\Application\Utility\IClassLog;
	use Zend\Mvc\Controller\Plugin\AbstractPlugin;

	class MyPlugin extends AbstractPlugin implements IClassLog
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