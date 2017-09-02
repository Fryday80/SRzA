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
			0 => '/Data',
		);

		const MEDIA_SERVICE = 0;

		/** @var  string $root Root directory of the website */
		private static $root;
		/** @var bool */
		private static $initialized = false;

		/** @var int|null $subType key from ::SUB_ROOTS */
		public static $subType = null;
		/** @var  string $subRoot contains value matching self::SUB_ROOTS  */
		private static $subRoot;

		/**
		 * Initialize replaces __construct()
		 *
		 * @param string $path used as reference due &$path => no return value
		 */
		private static function initialize(&$path){
			if (self::$initialized == true) return;
			$root = getcwd();
			self::$root = self::cleanPath( $root ); // direct call of self::cleanPath( getcwd() ) throws error!
			self::cleanPath($path);
			self::$initialized = true;
		}

		/**
		 * Returns Relative Path to given path <br/>
		 * 		can be used with reference due &$path
		 *
		 * @param string $path
		 *
		 * @return string
		 */
		public static function getRelativePath(&$path)
		{
			self::initialize($path);
			self::removeRoot($path);
			if (self::isSubRoot($path))
				$path = str_replace(self::SUB_ROOTS[self::$subType], '', $path);

			return $path;
		}

		//@todo need to check for clients root?? c: e.g. on windows systems... this will fail with linux server depends on logic of upload way
		/**
		 * Is Absolute checks if given path is an AbsolutePath
		 *
		 * @param string $path
		 *
		 * @return bool
		 */
		public static function isAbsolute($path)
		{
			self::initialize($path);
			return (strpos($path, self::$root) === false) ? false : true;
		}

		/**
		 * Get Absolute Path
		 *
		 * @param          $path
		 * @param int|null $type use this classes constants, Pathfinder::MEDIA_SERVICE eg.
		 *
		 * @return string        absolute path
		 */
		public static function getAbsolutePath($path, int $type = null)
		{
			self::initialize($path);
			$subFolder = '';

			// erase wrong arguments
			if ($type !== null)
				if (!isset(self::SUB_ROOTS[$type])) $type = null;

			if (self::isAbsolute($path)) return $path;

			if ($path[0] !== '/') $path = '/' . $path;

			$isSubRoot = self::isSubRoot($path);
			if (!$isSubRoot && $type !== null)
				$subFolder = self::SUB_ROOTS[$type];
			if ($isSubRoot && $type !== null) {
				if (!(self::$subRoot == self::SUB_ROOTS[ $type ]))
					$subFolder = self::SUB_ROOTS[ $type ];
			}

			return self::$root . $subFolder . $path;
		}

		/**
		 * Clean Path <br/>
		 * 		turns '\' in '/' <br/>
		 * 		can be used with reference due &$path
		 *
		 * @param string $path
		 *
		 * @return string
		 */
		public static function cleanPath(&$path)
		{
			return $path = str_replace('\\', '/', $path);
		}


		/**
		 * Removes Root Path  <br/>
		 * 		can be used with reference due &$path
		 *
		 * @param string $path
		 *
		 * @return string
		 */
		private static function removeRoot (&$path)
		{
			$path = str_replace(self::$root, '', $path);
			return $path = ($path[0] == '/') ? $path : '/' . $path;
		}

		/**
		 * Checks if given Path is a modules root path (subRoot)
		 *
		 * @param string $path
		 *
		 * @return bool
		 */
		private static function isSubRoot($path)
		{
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
							self::$subRoot = $subDir;
					}
				}
			}
			if (self::$subType !== null)
				return true;
			return false;
		}
	}
