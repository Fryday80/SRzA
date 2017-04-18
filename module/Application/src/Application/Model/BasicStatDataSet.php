<?php

namespace Application\Model;


use Auth\Service\AccessService;

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
    protected function getUserName(){
        return $this->accessService->getUserName();
    }
}