<?php
namespace xxx\Service;

use vakata\database\Exception;


Class ProfileService
{
    private $table;

    function __construct($table)
    {
        $this->table = $table;
    }
}