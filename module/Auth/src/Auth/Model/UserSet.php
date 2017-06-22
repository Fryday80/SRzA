<?php
namespace Auth\Model;


class UserSet
{
    public $data = array();

    public function __construct(Array $userArray = array())
    {
        $this->data = $userArray;
    }

    public function addUser(User $user)
    {
        $this->data[] = $user;
    }

    public function toArrayOfUsers()
    {
        return $this->data;
    }

    public function toArray()
    {
        $array = array();
        foreach ($this->data as $user)
            $array[] = get_object_vars($user);
        return $array;
    }
}