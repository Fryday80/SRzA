<?php
namespace Auth\Factory\Service;

use Application\Factory\Basic\MyDefaultFactory;
use Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\ServiceLocatorInterface;

class AuthServiceFactory extends MyDefaultFactory
{
    public function createService(ServiceLocatorInterface $sm) {
        parent::createService($sm);
        $dbAdapter = $this->get('Zend\Db\Adapter\Adapter');
        $dbTableAuthAdapter = new CredentialTreatmentAdapter($dbAdapter, 'users', 'email', 'password');
        $authService = new AuthenticationService();
        $authService->setAdapter($dbTableAuthAdapter);
        $authService->setStorage($this->get('Auth\Model\AuthStorage'));
        return $authService;
    }
}