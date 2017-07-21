<?php
	/**
	 * Created by IntelliJ IDEA.
	 * User: Fry
	 * Date: 21.07.2017
	 * Time: 10:38
	 */

	namespace Application\Model\AbstractModels;


	use Zarganwar\PerformancePanel\Register;

	abstract class TimeLog
	{

		const TIME_LOGGING = true;

		public static function timeLog($msg)
		{
			if (self::TIME_LOGGING)
				Register::add($msg);
		}

		public static function test()
		{
			bdump('test');
		}
	}