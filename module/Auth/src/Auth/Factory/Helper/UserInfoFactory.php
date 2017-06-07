<?php
namespace Auth\Factory\Helper;

use Zend\ServiceManager\FactoryInterface;
use Auth\View\Helper\UserInfo;
use Zend\ServiceManager\ServiceLocatorInterface;

class UserInfoFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm) {
        $pL = $sm->getServiceLocator();
        $storage = $pL->get('Auth\Model\AuthStorage');
        return new UserInfo($storage);
    }
}