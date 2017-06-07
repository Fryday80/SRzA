<?php
namespace Auth\Factory\Helper;

use Application\Factory\Basic\MyDefaultFactory;
use Auth\View\Helper\UserInfo;
use Zend\ServiceManager\ServiceLocatorInterface;

class UserInfoFactory extends MyDefaultFactory
{
    public function createService(ServiceLocatorInterface $sm) {
        parent::createService($sm);
        $storage = $this->get('Auth\Model\AuthStorage');
        return new UserInfo($storage);
    }
}