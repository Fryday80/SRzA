<?php
namespace Application\DataObjects;


class ActiveUsers
{
    public $activeUserList;

    function __construct($dbResults)
    {
        $this->activeUserList = $dbResults;
    }

    public function toArray()
    {
        return $this->activeUserList;
    }

}