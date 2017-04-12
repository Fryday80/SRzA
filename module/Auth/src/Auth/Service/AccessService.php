<?php
namespace Auth\Service;

use Zend\Authentication\AuthenticationService;
use Zend\View\Helper\Navigation\AbstractHelper;
use Auth\Model\AuthStorage;

class AccessService {
    protected $aclService;
    protected $authService;
    protected $acl;
    protected $hasIdentity = false;
    protected $role;
    protected $userID;
    protected $userName;
    protected $userIP;

    function __construct(AclService $aclService, AuthenticationService $authService, AuthStorage $storage) {
        $this->aclService = $aclService;
        $this->authService = $authService;
        $this->role = $storage->getRoleName();
        $this->userID = $storage->getUserID();
        $this->userName = $storage->getUserName();
        $this->userIP = $storage->getIP();
        $this->acl = $aclService;
        $this->acl->initAcl();
        
        AbstractHelper::setDefaultAcl($this->acl);
        AbstractHelper::setDefaultRole($this->role);
    }

    /**
     * @param $resoure
     * @param $permission
     * @return bool
     */
    function allowed($resoure, $permission) {
        return $this->acl->isAccessAllowed($this->role, $resoure, $permission);
    }

    /**
     * @return bool
     */
    function hasIdentity() {
        return $this->authService->hasIdentity();
    }

    /**
     * @return AclService
     */
    function getAcl() {
        return $this->acl;
    }

    /**
     * @return string
     */
    function getRole() {
        return $this->role;
    }

    /**
     * @return int|mixed
     */
    function getUserID() {
        return $this->userID;
    }

    /**
     * @return mixed|string
     */
    function getUserName() {
        return $this->userName;
    }

    /**
     * @return int|mixed
     */
    function getUserIP() {
        return $this->userIP;
    }

    /**
     * @return array
     */
    public function fetchAllRoles(){
        return $this->aclService->fetchAllRoles();
}
}