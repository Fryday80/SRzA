<?php
namespace Auth\Service;


use Zend\Authentication\AuthenticationService;

class AccessService {
    protected $aclService;
    protected $authService;
    protected $acl;
    protected $hasIdentity = false;
    
    function __construct(AclService $aclService, AuthenticationService $authService) {
        $this->aclService = $aclService;
        $this->authService = $authService;
        
        $user = 'guest';
        //@todo check identity and set user name
        $this->acl = $aclService->getAcl($user);
    }
    function allowed($resoure) {
        if ($this->hasIdentity) {
            
        }
    }
}