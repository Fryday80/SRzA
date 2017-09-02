<?php
	namespace Media\Utility;

	use Exception;

	class Pathfinder
	{
		/**
		 * Initialize replaces __construct()
		 *
		 * @param string $path used as reference due &$path => no return value
		 */
		private static function initialize(&$path){
			if (self::$initialized == true) return;
			$root = getcwd() . '/Data/';
			self::$root = self::cleanPath( $root ); // direct call of self::cleanPath( getcwd() ) throws error!
			self::cleanPath($path);
			self::$initialized = true;
		}

		/**
		 * Returns Relative Path to given path
		 *
		 * @param string $path
		 *
		 * @return string
		 */
		public static function getRelativePath($path)
		{
			$root = str_replace('\\', '/', getcwd() . '/Data/');
			$path = str_replace('\\', '/', $path);
			$path = str_replace($root, '', $path);

			return $path;
		}

		/**
		 * Is Absolute checks if given path is an AbsolutePath
		 *
		 * @param string $path
		 *
		 * @return bool
		 * @throws Exception
		 */
		public static function isAbsolute($path)
		{
			//  /fullPath
			//  /media/file/path
			//  path

			if(strpos($path, './') || strpos($path, '../')) throw new Exception("Path forbidden");

			$root = str_replace('\\', '/', getcwd() . '/Data/');
			$path = str_replace('\\', '/', $path);

			return (strpos($path, $root) === false) ? false : true;
		}

		/**
		 * Get Absolute Path
		 *
		 * @param  string    $path
		 *
		 * @return string    absolute path
		 * @throws Exception
		 *
		 */
		public static function getAbsolutePath($path)
		{
			if ($path[0] == '/') throw new Exception("Path forbidden");

			return getcwd() . '/Data' . $path;
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
	}