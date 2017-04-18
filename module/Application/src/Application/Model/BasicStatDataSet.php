<?php

namespace Application\Model;


class BasicStatDataSet
{
    /** @var  $accessService AccessService */
    protected $accessService;


    function __construct($accessService)
    {
        $this->accessService = $accessService;
    }

    protected function getUserId(){
        return ($this->accessService->getUserID() == "-1")? 0 : (int)$this->accessService->getUserID();
    }
}