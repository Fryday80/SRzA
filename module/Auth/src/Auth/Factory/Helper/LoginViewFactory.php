<?php
namespace Auth\Factory\Helper;

use Application\Factory\Basic\MyDefaultFactory;
use Auth\View\Helper\LoginView;
use Zend\ServiceManager\ServiceLocatorInterface;

class LoginViewFactory extends MyDefaultFactory
{
    public function createService(ServiceLocatorInterface $sm) {
        parent::createService($sm);
        $storage = $this->get('Auth\Model\AuthStorage');
        $loginview = new LoginView($storage);
        return $loginview;
    }
}