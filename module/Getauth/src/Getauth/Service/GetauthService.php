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
        $data = $this->usertable->fetchAll();
        return $this->cleanData($data->toArray());
    }

    public function getUser ($id){

        $user =  $this->usertable->getUser($id);
        $details = $this->usertable->getUserDetails(array('id' => $id));
        
        return array (
            'user' => $user,
            'details' => $details,
        );
    }

    public function x (){}

    public function y (){}

    public function z (){}

    public function xx (){}

    private function cleanData ($data){
        $new_data = array ();
        $result = array ();
        $remove_array = array('password');

        foreach ($data as $count){
            foreach ($count as $key => $value) {
                if (!in_array($key, $remove_array)) {
                    $new_data[$key] = $value;
                }
            }
            array_push($result, $new_data);
        }
        return $result;
    }
}