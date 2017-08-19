<?php

namespace Application\Service;

const CACHE_PATH = '/cache'; //relative to root, start with /
class CacheService
{
    private $cachePath;
    private $fileExtension = '.cache';
    // in memory cache
    private $cachedCache = array ();
    private $changedCache = array();

    public function __construct() {
        $this->cachePath = realpath(getcwd().CACHE_PATH).'/';
    }

    public function getCacheList() {
        return $this->recursiveCacheInfo($this->cachePath)['childes'];
    }
    private function recursiveCacheInfo($path) {
        $childItems = [];
        if (!is_file($path)) {
            $childes = scandir($path);
            foreach($childes as $child) {
                if ($child == '.' || $child == '..') continue;
                $childPath = realpath($path.'/'.$child);
                array_push($childItems, $this->recursiveCacheInfo($childPath) );
            }
        }
        //windows <=> unix path fix
        $cachePath = str_replace('\\', '/', $this->cachePath);
        $path = str_replace('\\', '/', $path);

        $relativePath = str_replace($cachePath, '', $path);
        if ($relativePath == '') {
            $itemName = '_ROOT_';
        } else {
            $itemName = str_replace('.cache', '', $relativePath);
        }
        $sizeInBytes = filesize($path);
        $size = $this->FileSizeConvert($sizeInBytes);
        $item = array(
            'name' => $itemName,
            'size' => $size,
            'sizeInBytes' => $sizeInBytes,
            'childes' => $childItems
        );
        return $item;
    }
    /**
     * @param $name string form 'nav/main'
     * @param $content string|mixed(serializable)
     * @param bool $serialize
     */
    public function setCache($name, $content, $serialize = true) {
		$this->changedCache[$name] = array ($name, $serialize);
		$this->cachedCache[$name]  = $content;

    }

    /**
     * @param $name string form 'nav/main'
     * @param bool $serialize
     * @return string|mixed(serializable)|false
     */
    public function getCache($name, $serialize = true) {
    	if (isset ($this->cachedCache[$name])){
    		return $this->cachedCache[$name];
		}
        if ($this->exists($name)) {
			$this->cachedCache[$name] = $this->loadFile($name, $serialize);
			return $this->cachedCache[$name];
        }
        return false;
    }

    /**
     * @param $name string form 'nav/main'
     * @return bool
     */
    public function hasCache($name) {
        return $this->exists($name);
    }

    /**
     * @param $name string|null
     * @return bool|void
     */
    public function clearCache($name) {
        if (!$name) {
            //clear hole cache
			//
			// clear in memory cache
			$this->changedCache = array();
			$this->cachedCache  = array();

			//@todo test if this goes right
			// clear on disc
            $items = scandir($this->cachePath, 1);
            foreach ($items as $item) {
                $this->deleteRecursive($this->cachePath.'/'.$item);
            }
        }
        else
		{
			// clear in memory cache
			unset ($this->changedCache[$name]);
			unset ($this->cachedCache[$name]);
		}

		// clear on disc
        if (!$this->exists($name))
            return false;

        if(is_dir($name)){
            $this->unsetFolder($name);
            return;
        } else {
            $this->unsetFile($name);
        }
    }

	public function onFinish()
	{
		// in memory cache
		// saves changes to disc
		foreach ($this->changedCache as $name => $value) {
			$this->saveFile($name, $this->cachedCache[$name], $value[1]);
			// remove "changed" flag
			unset($this->changedCache[$name]);
		}
    }

    private function unsetFile($name){
        unlink($this->realPath($name));
    }
    private function unsetFolder($name){
        $this->deleteRecursive($this->realPath($name));
    }
    /**
     * @param $realPath
     */
    private function deleteRecursive($realPath) {
        if (is_dir($realPath)){
            $files = glob($realPath.'/*', GLOB_MARK); //GLOB_MARK adds a slash to directories returned
            foreach ($files as $file) {
                $this->deleteRecursive( $file );
            }
            rmdir($realPath);
        } elseif (is_file($realPath)) {
            unlink($realPath);
        }
    }

    private function exists($name) {
        return file_exists($this->realPath($name));
    }
    private function loadFile($name, $serialize = true) {
        if (!$this->exists($name))
            return false;

        $content = file_get_contents($this->realPath($name));
        if ($serialize)
            $content = unserialize($content);

        return $content;
    }
    private function saveFile($name, $content, $serialize = true) {
        if ($serialize)
            $content = serialize($content);


        $folders = explode('/', $name);
        $file = array_pop($folders);
        $lastPath = $this->cachePath;
        if(substr($lastPath, -1) == '/') {
            $lastPath = substr($lastPath, 0, -1);
        }
        foreach ($folders as $key => $value) {
            $path = $lastPath.'/'.$value;
            if (!is_dir($path))
                mkdir($path);
            $lastPath = $path;
        }

        $a = file_put_contents($this->realPath($name), $content);

        return true;
    }

    private function realPath($name) {
        if (!$name || $name == '')
            return false;

        $path = $this->cachePath.$name.$this->fileExtension;
        return $path;
    }

    /**
     * Converts bytes into human readable file size.
     *
     * @param string $bytes
     * @return string human readable file size (2,87 Мб)
     * @author Mogilev Arseny
     */
    function FileSizeConvert($bytes)
    {
        $bytes = floatval($bytes);
        $arBytes = array(
            0 => array(
                "UNIT" => "TB",
                "VALUE" => pow(1024, 4)
            ),
            1 => array(
                "UNIT" => "GB",
                "VALUE" => pow(1024, 3)
            ),
            2 => array(
                "UNIT" => "MB",
                "VALUE" => pow(1024, 2)
            ),
            3 => array(
                "UNIT" => "KB",
                "VALUE" => 1024
            ),
            4 => array(
                "UNIT" => "B",
                "VALUE" => 1
            ),
        );
        $result = 0;
        foreach($arBytes as $arItem)
        {
            if($bytes >= $arItem["VALUE"])
            {
                $result = $bytes / $arItem["VALUE"];
                $result = str_replace(".", "," , strval(round($result, 2)))." ".$arItem["UNIT"];
                break;
            }
        }
        return $result;
    }

}