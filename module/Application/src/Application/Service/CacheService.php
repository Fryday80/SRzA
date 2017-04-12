<?php

namespace Application\Service;

const CACHE_PATH = '/cache'; //relative to root, start with /
class CacheService
{
    private $cachePath;
    private $fileExtension = '.cache';

    public function __construct() {
        $this->cachePath = realpath(getcwd().CACHE_PATH).'/';
    }

    /**
     * @param $name in form 'nav/main'
     * @param $content string|mixed(serializable)
     * @param bool $serialize
     */
    public function setCache($name, $content, $serialize = true) {
        $this->saveFile($name, $content, $serialize);
    }

    /**
     * @param $name in form 'nav/main'
     * @param bool $serialize
     * @return string|mixed(serializable)|false
     */
    public function getCache($name, $serialize = true) {
        if ($this->exists($name)) {
            return $this->loadFile($name, $serialize);
        }
        return false;
    }

    /**
     * @param $name in form 'nav/main'
     * @return bool
     */
    public function hasCache($name) {
        return $this->exists($name);
    }

    private function exists($name) {
        return file_exists($this->realPath($name));
    }
    private function loadFile($name, $serialize = true) {
        if (!$this->exists($name))
            return false;

        $content = file_get_contents($this->realPath($name));
        if ($serialize)
            $content = json_decode($content);

        return $content;
    }
    private function saveFile($name, $content, $serialize = true) {
        if ($serialize)
            $content = json_encode($content);


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
}