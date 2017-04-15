<?php

namespace Application\DataObjects;


class BasicDashboardDataSet
{
    public $data;

    function __construct($data = null)
    {
        $this->data = $data;
    }

    public function toArray()
    {
        return $this->data;
    }
}