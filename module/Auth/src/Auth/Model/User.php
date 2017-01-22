<?php
namespace Auth\Model;

class User
{
    public $id;
    public $email;
    public $name;
    public $password;
    public $status;

    public function exchangeArray($data)
    {
        $this->id           = (! empty($data['id']))            ? $data['id']           : $this->id;
        $this->email        = (! empty($data['email']))         ? $data['email']        : $this->email;
        $this->name         = (! empty($data['name']))          ? $data['name']         : $this->name;
        $this->password     = (! empty($data['password']))      ? $data['password']     : $this->password;
        $this->status       = (! empty($data['status']))        ? $data['status']       : $this->status;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
