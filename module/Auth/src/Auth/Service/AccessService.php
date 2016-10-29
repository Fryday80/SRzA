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
    
    function __construct(AclService $aclService, AuthenticationService $authService, AuthStorage $storage) {
        $this->aclService = $aclService;
        $this->authService = $authService;
        $this->role = $storage->getRoleName();
        $this->acl = $aclService;
        $this->acl->initAcl();
        
        AbstractHelper::setDefaultAcl($this->acl);
        AbstractHelper::setDefaultRole($this->role);
    }
    function allowed($resoure, $permission) {
        //print("<br>");
        //print($this->role);
        //print("<br>");
        //print($resoure);
        //print("<br>");
        //print($permission);
        //die;
        return $this->acl->isAccessAllowed($this->role, $resoure, $permission);
    }
    function hasIdentity() {
        return $this->authService->hasIdentity();
    }
}