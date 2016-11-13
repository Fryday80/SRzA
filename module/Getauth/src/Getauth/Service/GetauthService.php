<?php
namespace Getauth\Service;

use vakata\database\Exception;

Class GetauthService
{
    private $authStorage;
    private $usertable;

    function __construct($authStorage, $usertable)
    {
        $this->authStorage = $authStorage;
        $this->usertable = $usertable;
    }

    public function getAllUsers (){
        return $this->usertable->fetchAll();
    }

    public function getUser ($id){
        /*
        $user =  $this->usertable->getUser($id);
        $detail =  $this->usertable->getUserDetails(array('id' => $id));
        return array(
            'user' => $user,
            'detail' => $detail,
        );
        */
    }

    public function x (){}

    public function y (){}

    public function z (){}

    public function xx (){}

    public function xy (){}

}