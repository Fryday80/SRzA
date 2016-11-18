<?php
namespace Auth;

use Auth\Model\UserTable;
use Auth\Model\RoleTable;
use Auth\Model\PermissionTable;
use Auth\Model\ResourceTable;
use Auth\Model\RolePermissionTable;
use Auth\Model\UserRoleTable;
use Auth\Service\AclService;
use Auth\Service\AccessService;
use Auth\Model\User;
use Auth\View\Helper\LoginView;
use Auth\Model\AuthStorage;
use Auth\View\Helper\UserInfo;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\ModuleRouteListener;
use Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Stdlib\Hydrator\ObjectProperty;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\Navigation\AbstractHelper;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface, ViewHelperProviderInterface
{
    private $whitelist = array(
        'Auth\Controller\Auth-login',
        'Auth\Controller\Auth-logout',
        'Cms\Controller\Page-index'
    );

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        
        $eventManager->attach('dispatch', array($this, 'checkLogin'));
        return $this;
    }

    public function checkLogin($e)
    {
        $accessService      = $e->getApplication()->getServiceManager()->get('AccessService');
        $target             = $e->getTarget();
        $match              = $e->getRouteMatch();
        $controller         = $match->getParam('controller');
        $action             = $match->getParam('action');
        $title               = $match->getParam('title');
        $requestedResourse  = $controller . "-" . $action;


        //dump($controller, $action, 1);  //cleanfix bugfix
        if( !in_array($requestedResourse, $this->whitelist)){
            if( !$accessService->allowed($controller, $action) ){
//mach mal den stackTrace raus pls
                //dump("No Permission", 'BUGGY####', 1);
                //return $target->redirect()->toUrl('/login');
            }
        }
        AbstractHelper::setDefaultAcl($accessService->getAcl());
        AbstractHelper::setDefaultRole($accessService->getRole());
    }
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
                )
            )
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'userinfo' => function (ServiceLocatorInterface $serviceLocator) {
                    $storage = $serviceLocator->getServiceLocator()->get('Auth\Model\AuthStorage');
                    $userInfo = new UserInfo($storage);
                    return $userInfo;
                },
                'loginview' => function (ServiceLocatorInterface $serviceLocator) {
                    $storage = $serviceLocator->getServiceLocator()->get('Auth\Model\AuthStorage');
                    $loginview = new LoginView($storage);
                    return $loginview;
                }
            )
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Auth\Model\AuthStorage' => function ($sm) {
                    $storage = new Model\AuthStorage('sra');
                    return $storage;
                },
                'AuthService' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $dbTableAuthAdapter = new CredentialTreatmentAdapter($dbAdapter, 'users', 'email', 'password');
                    $authService = new AuthenticationService();
                    $authService->setAdapter($dbTableAuthAdapter);
                    $authService->setStorage($sm->get('Auth\Model\AuthStorage'));
                    return $authService;
                },
                'AccessService' => function ($serviceManager) {
                    $storage = $serviceManager->get('Auth\Model\AuthStorage');
                    $aclService = $serviceManager->get('Auth\AclService');
                    $authService = $serviceManager->get('AuthService');
                    return new AccessService($aclService, $authService, $storage);
                },
                'Auth\AclService' => function ($serviceManager) {
                    return new AclService();
                },
                'Auth\Model\UserTable' => function ($serviceManager) {
                    $resultSetPrototype = new HydratingResultSet();
                    $resultSetPrototype->setHydrator(new ObjectProperty());
                    $resultSetPrototype->setObjectPrototype(new User());
                    return new UserTable($serviceManager->get('Zend\Db\Adapter\Adapter'), $resultSetPrototype);
                },
                'Auth\Model\RoleTable' => function ($serviceManager) {
                    return new RoleTable($serviceManager->get('Zend\Db\Adapter\Adapter'));
                },
                'Auth\Model\UserRoleTable' => function ($serviceManager) {
                    return new UserRoleTable($serviceManager->get('Zend\Db\Adapter\Adapter'));
                },
                'Auth\Model\PermissionTable' => function ($serviceManager) {
                    return new PermissionTable($serviceManager->get('Zend\Db\Adapter\Adapter'));
                },
                'Auth\Model\ResourceTable' => function ($serviceManager) {
                    return new ResourceTable($serviceManager->get('Zend\Db\Adapter\Adapter'));
                },
                'Auth\Model\RolePermissionTable' => function ($serviceManager) {
                    return new RolePermissionTable($serviceManager->get('Zend\Db\Adapter\Adapter'));
                }
            )
        );
    }

}
