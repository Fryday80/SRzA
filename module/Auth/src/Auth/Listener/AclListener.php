<?php

namespace Auth\Listener;


use Zend\EventManager\AbstractListenerAggregate;
use Auth\Service\AclService;
use Zend\Authentication\AuthenticationService;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;

class AclListener extends AbstractListenerAggregate {

    private $aclService;
    private $authService;

    public function __construct(AclService $aclService, AuthenticationService $authService) {
        $this->aclService = $aclService;
        $this->authService = $authService;
    }

    public function attach(EventManagerInterface $eventManager) {
        $this->listeners[] = $eventManager->attach(MvcEvent::EVENT_ROUTE, array($this, 'checkAcl'), -1000);
    }

    public function checkAcl(MvcEvent $e) {
        $role = !$this->getAuthService()->hasIdentity() ? AclService::USER_GUEST : $this->getAuthService()->getIdentity()->getRole();

        if (!$this->getAclService()->isAllowed($role, $e->getRouteMatch()->getMatchedRouteName())) {
            $e->getRouteMatch()->setParam('controller', 'Auth\Controller\Auth')
                    ->setParam('action', 'accessDenied');
        }
        return $this;
    }

    private function getAclService() {
        return $this->aclService;
    }

    private function getAuthService() {
        return $this->authService;
    }

}