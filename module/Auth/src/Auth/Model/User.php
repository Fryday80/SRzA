<?php
namespace Auth\Model;

class User
{
    public $id;
    public $email;
    public $password;

    public $name;
    public $sureName;
    public $gender;
    public $vita;
    public $familyID;
    public $familyOrder;

    public function exchangeArray($data)
    {
        $this->id           = (! empty($data['id']))            ? $data['id']           : $this->id;
        $this->email        = (! empty($data['email']))         ? $data['email']        : $this->email;
        $this->password     = (! empty($data['password']))      ? $data['password']     : $this->password;
        $this->name         = (! empty($data['name']))          ? $data['name']         : $this->name;
        $this->sureName     = (! empty($data['sure_name']))     ? $data['sure_name']    : $this->sureName;
        $this->gender       = (! empty($data['gender']))        ? $data['gender']       : $this->gender;
        $this->vita         = (! empty($data['vita']))          ? $data['vita']         : $this->vita;
        $this->familyID     = (! empty($data['family_id']))     ? $data['family_id']    : $this->familyID;
        $this->familyOrder  = (! empty($data['family_order']))  ? $data['family_order'] : $this->familyOrder;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
