<?php
namespace Auth\Factory\Helper;

use Zend\ServiceManager\FactoryInterface;
use Auth\View\Helper\LoginView;
use Zend\ServiceManager\ServiceLocatorInterface;

class LoginViewFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $pL = $sm->getServiceLocator();
        $storage = $pL->get('Auth\Model\AuthStorage');
        $statsService = $pL->get('StatisticService');
        $loginview = new LoginView($storage, $statsService);
        return $loginview;
    }
}