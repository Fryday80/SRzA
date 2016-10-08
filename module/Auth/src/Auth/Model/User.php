<?php
namespace Auth\Model;

use Zend\InputFilter\InputFilter;

class User
{
    public $id;
    public $email;
    public $name;
    public $password;
    
    protected $inputFilter;
    
    public function exchangeArray($data)
    {
        $this->id = (! empty($data['id'])) ? $data['id'] : $this->id;
        $this->email = (! empty($data['email'])) ? $data['email'] : $this->email;
        $this->name = (! empty($data['name'])) ? $data['name'] : $this->name;
        $this->password = (! empty($data['password'])) ? $data['password'] : $this->password;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
