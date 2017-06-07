<?php
namespace Auth\Factory\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\ServiceLocatorInterface;

class AuthServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
        $dbTableAuthAdapter = new CredentialTreatmentAdapter($dbAdapter, 'users', 'email', 'password');
        $authService = new AuthenticationService();
        $authService->setAdapter($dbTableAuthAdapter);
        $authService->setStorage($sm->get('Auth\Model\AuthStorage'));
        return $authService;
    }
}