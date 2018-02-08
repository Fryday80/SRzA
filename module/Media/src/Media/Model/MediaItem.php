<?php
namespace Media\Model;

class MediaItem {
    public $name;
    public $type;
    public $fullPath;
    public $parentPath;
    public $path;
    public $livePath;
    public $size;
    public $extension = '';
    public $readable  = 0;
    public $writable  = 0;
    public $created   = '';
    public $modified  = '';
    public $timestamp = '';

    function __construct() {}
}