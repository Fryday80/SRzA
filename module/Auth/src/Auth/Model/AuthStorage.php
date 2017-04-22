<?php
namespace Auth\Model;

use Zend\Authentication\Storage\Session;

class AuthStorage extends Session
{

    public function setRememberMe($rememberMe = 0, $time = 1209600)
    {
        if ($rememberMe == 1) {
            $this->session->getManager()->rememberMe($time);
        }
    }
    public function getManager() {
        return $this->session->getManager();
    }
    public function forgetMe()
    {
        $this->session->offsetSet('userId', 0);
        $this->session->offsetUnset('userName');
        $this->session->offsetUnset('roleId');
        $this->session->offsetUnset('roleName');
        $this->session->getManager()->forgetMe();
    }
    public function hasIdentity() {
        if ($this->session->offsetExists('userId')) {
            if ($this->session->offsetGet('userId') > 0) {
                return true;
            }
            return false;
        }
        return false;
    }
    public function getUserID() {
        if ($this->session->offsetExists('userId')) {
            return $this->session->offsetGet('userId');
        }
        return -1;
    }
    public function setUserID($id) {
        $this->session->offsetSet('userId', $id);
    }
    public function getUserName() {
        if ($this->session->offsetExists('userName')) {
            return $this->session->offsetGet('userName');
        }
        return '';
    }
    public function setUserName($name) {
        $this->session->offsetSet('userName', $name);
    }
    public function getRoleID() {
        if ($this->session->offsetExists('roleId')) {
            return $this->session->offsetGet('roleId');
        }
        return -1;
    }
    public function setRoleID($id) {
        $this->session->offsetSet('roleId', $id);
    }
    public function getRoleName() {
        if ($this->session->offsetExists('roleName')) {
            return $this->session->offsetGet('roleName');
        }
        return 'Guest';
    }
    public function setRoleName($id) {
        $this->session->offsetSet('roleName', $id);
    }

    public function getIP() {
        if ($this->session->offsetExists('ip')) {
            return $this->session->offsetGet('ip');
        }
        return null;
    }
    public function setIP($ip) {
        $this->session->offsetSet('ip', $ip);
    }
}