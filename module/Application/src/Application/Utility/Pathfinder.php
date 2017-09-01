<?php
	/**
	 * Created by IntelliJ IDEA.
	 * User: Fry
	 * Date: 01.09.2017
	 * Time: 01:13
	 */

	namespace Application\Utility;

	class Pathfinder
	{
		const SUB_ROOTS = array(
			0 => '/Data'
		);

		// all these types are linked to MediaService or MediaItems
		const MEDIA_SERVICE = 0;
		const MEDIA_ITEM = 0;
		const DATA  = 0;
		const IMAGE = 0;  // images are handled like data

		/** @var  string $root Root directory of the website */
		private static $root;
		/** @var bool */
		private static $initialized = false;

		/** @var int|null $subType key from ::SUB_ROOTS */
		private static $subType = null;

		private static function initialize(){
			if (self::$initialized == true) return;
			self::$root = self::cleanPath( getcwd() );
			self::$initialized = true;
		}

		public static function getRelativePath($path)
		{
			self::initialize();
			$path = self::cleanPath($path);
			$path = str_replace(self::$root, '', $path);
			if (self::isSubRoot($path))
				$path = str_replace(self::SUB_ROOTS[self::$subType], '', $path);

			return $path;

		}

		public static function getAbsolutePath($path, int $type = null)
		{
			self::initialize();
			$path = self::cleanPath($path);
			$subFolder = '';

			if ($type !== null)
				$subFolder = self::SUB_ROOTS[$type];

			if ($path[0] !== '/')
				$path = '/' . $path;

			return self::$root . $subFolder . $path;
		}

		public static function isAbsolute($path)
		{
			self::initialize();
			$path = self::cleanPath($path);
			return (strpos($path, self::$root) === false) ? false : true;
		}

		public static function cleanPath($path)
		{
			return str_replace('\\', '/', $path);
		}

		private static function isSubRoot($path)
		{
			self::initialize();
			$path = self::cleanPath($path);
			// remove root
			if (self::isAbsolute($path))
				$path = str_replace(self::$root, '', $path);

			foreach (self::SUB_ROOTS as $key => $subDir)
			{
				$subDirLength = strlen($subDir);
				if (substr($path, 0, $subDirLength) == $subDir)
				{
					switch ($key)
					{
						case self::MEDIA_SERVICE:
							self::$subType = self::MEDIA_SERVICE;
					}
				}
			}
			if (self::$subType !== null)
				return true;
			return false;
		}
	}
