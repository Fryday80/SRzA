<?php
	/**
	 * Created by IntelliJ IDEA.
	 * User: Fry
	 * Date: 24.08.2017
	 * Time: 15:06
	 */

	namespace module\Application\src\Application\Utility;


	interface IClassLog
	{
		public function getLog();
		public function log($method, $msg = null);
	}