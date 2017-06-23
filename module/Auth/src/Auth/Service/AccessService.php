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
    public $session;

    function __construct(AclService $aclService, AuthenticationService $authService, AuthStorage $storage) {
        $this->session = $storage;
        $this->aclService = $aclService;
        $this->authService = $authService;
        $this->role = $storage->getRoleName();
        $this->userID = $storage->getUserID();
        $this->userName = $storage->getUserName();
        $this->userIP = $storage->getIP();
        $this->acl = $aclService;
        
        AbstractHelper::setDefaultAcl($this->acl);
        AbstractHelper::setDefaultRole($this->role);
    }

    /**
     * @param $resource
     * @param $permission
     * @return bool
     */
    function allowed($resource, $permission) {
        return $this->acl->isAccessAllowed($this->role, $resource, $permission);
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
     * @return int
     */
    function getUserID() {
        return (int)$this->userID;
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
}