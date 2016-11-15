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

    function __construct(AclService $aclService, AuthenticationService $authService, AuthStorage $storage) {
        $this->aclService = $aclService;
        $this->authService = $authService;
        $this->role = $storage->getRoleName();
        $this->userID = $storage->getUserID();
        $this->userName = $storage->getUserName();
        $this->acl = $aclService;
        $this->acl->initAcl();
        
        AbstractHelper::setDefaultAcl($this->acl);
        AbstractHelper::setDefaultRole($this->role);
    }
    function allowed($resoure, $permission) {
        return $this->acl->isAccessAllowed($this->role, $resoure, $permission);
    }
    function hasIdentity() {
        return $this->authService->hasIdentity();
    }
    function getAcl() {
        return $this->acl;
    }
    function getRole() {
        return $this->role;
    }
    function getUserID() {
        return $this->userID;
    }
    function getUserName() {
        return $this->userName;
    }
}