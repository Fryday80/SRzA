<?php
namespace Auth\Model;

class User
{
    public $id;
    public $email;
    public $name;
    public $password;
    public $status;
    public $role_id;
    public $role_name;
    public $created_on;
    public $modified_on;
    public $street;
    public $city;
    public $zip;
    public $member_number;
    public $real_name;
    public $real_surename;
    public $birthday;
    public $gender;
    public $user_image;

    public function exchangeArray($data)
    {
        $this->id            = (! empty($data['id']))            ? $data['id']            : $this->id;
        $this->email         = (! empty($data['email']))         ? $data['email']         : $this->email;
        $this->name          = (! empty($data['name']))          ? $data['name']          : $this->name;
        $this->password      = (! empty($data['password']))      ? $data['password']      : $this->password;
        $this->status        = (isset($data['status']))        ? $data['status']        : $this->status;
        $this->role_id       = (! empty($data['role_id']))       ? $data['role_id']       : $this->role_name;
        $this->role_name     = (! empty($data['role_name']))     ? $data['role_name']     : $this->role_name;
        $this->created_on    = (! empty($data['created_on']))    ? $data['created_on']    : $this->created_on;
        $this->modified_on   = (! empty($data['modified_on']))   ? $data['modified_on']   : $this->modified_on;
        $this->street        = (! empty($data['street']))        ? $data['street']        : $this->street;
        $this->city          = (! empty($data['city']))          ? $data['city']          : $this->city;
        $this->zip           = (! empty($data['zip']))           ? $data['zip']           : $this->zip;
        $this->member_number = (! empty($data['member_number'])) ? $data['member_number'] : $this->member_number;
        $this->real_name     = (! empty($data['real_name']))     ? $data['real_name']     : $this->real_name;
        $this->real_surename = (! empty($data['real_surename'])) ? $data['real_surename'] : $this->real_surename;
        $this->birthday      = (! empty($data['birthday']))      ? $data['birthday']      : $this->birthday;
        $this->gender        = (! empty($data['gender']))        ? $data['gender']        : $this->gender;
//        $this->user_image = '/_users/'.$this->name.'/profileImage.jpg';
        $this->user_image    = (! empty($data['user_image']))    ? $data['user_image']    : $this->user_image;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
