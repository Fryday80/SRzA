<?php
namespace Auth\Factory\Service;

use Application\Factory\Basic\MyDefaultFactory;
use Auth\Service\AccessService;
use Zend\ServiceManager\ServiceLocatorInterface;

class AccessServiceFactory extends MyDefaultFactory
{
    public function createService(ServiceLocatorInterface $sm) {
        parent::createService($sm);
        $storage     = $this->get('Auth\Model\AuthStorage');
        $aclService  = $this->get('Auth\AclService');
        $authService = $this->get('AuthService');
        return new AccessService($aclService, $authService, $storage);
    }
}