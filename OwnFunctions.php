<?php
	/**
	 * Created by IntelliJ IDEA.
	 * User: Fry
	 * Date: 19.09.2017
	 * Time: 14:27
	 */


//debugging
//function dump ($varray = 'Dump test', $marker = '##########BUGFIX######', $trace=3)
//{
//    echo $marker;
//    echo ('<pre style="background-color: #0ac2d2">');
//    var_dump($varray);
//    echo ('</pre>');
//    echo ('<pre style="background-color: #949c53">');
//    //debug_print_backtrace (null, $trace);
//    echo ('</pre>');
//
//}
//
//function dumpd ($varray = 'Dump test', $marker = '##########BUGFIX######', $trace=3){
//    dump ($varray, $marker, $trace);
//    die;
//}
	/**
	 * Replace the LAST occurrence of an expression
	 * @param $search
	 * @param $replace
	 * @param $str
	 *
	 * @return mixed
	 */
	function str_replace_last( $search , $replace , $str ) {
		if( ( $pos = strrpos( $str , $search ) ) !== false ) {
			$search_length  = strlen( $search );
			$str    = substr_replace( $str , $replace , $pos , $search_length );
		}
		return $str;
	}

	/**
	 * Gets the extension of given file path <br/>
	 * returns null if $filePath has no extension (no '.sth' part)
	 *
	 * @param string $filePath path string to search in
	 *
	 * @return null|string null | file extension without '.'
	 */
	function getExtension($filePath){
		$lastDot = strrpos($filePath, '.');
		if($lastDot === false)
			return false;
		return substr($filePath, $lastDot+1);
	}

	/**
	 * Gets the string after $delimiter <br/>
	 * returns $string if $delimiter wasn't found
	 *
	 * @param string $string	  string to search in
	 * @param string $delimiter   string to search for
	 *
	 * @return string 			  original string if $delimiter not found | string from $delimiter on
	 */
	function getFromLast($string, $delimiter){
		$lastOccurrence = strrpos($string, $delimiter);
		if($lastOccurrence === false)
			return $string;
		return substr($string, $lastOccurrence+1);
	}

	/**
	 * Reads Dir Recursive <br/>
	 * always ignores '.' and '..'
	 *
	 * @param string $path absolute path to folder
	 *
	 * @param array  $ignore array of expressions to ignore
	 *
	 * @return array|null array of files that preserves folder tree structure
	 */
	function readDirRecursive($path,  $ignore = array())
	{
		$result = null;
		array_push($ignore, '.');
		array_push($ignore, '..');

		if ( is_dir ( $path))
		{
			if ( $handle = opendir($path) )
			{
				while (($file = readdir($handle)) !== false)
				{
					if (in_array($file, $ignore)) continue;
					if ($path[strlen($path)-1] !== '/') $path .= '/';

					if (is_dir($path.$file)) $result[$file] = readDirRecursive($path.$file, array('folder.conf'));
					else $result[$file] = $file;
				}
				closedir($handle);
			}
			return $result;
		}
	}