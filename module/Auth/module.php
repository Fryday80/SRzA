<?php
namespace Auth;

use Auth\Model\UserTable;
use Auth\Model\RoleTable;
use Auth\Model\PermissionTable;
use Auth\Model\ResourceTable;
use Auth\Model\RolePermissionTable;
use Auth\Model\UserRoleTable;
use Auth\Service\AclService;
use Auth\Utility\UserPassword;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\ModuleRouteListener;
use Zend\Session\Container;
use Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter;
use Zend\Authentication\AuthenticationService;
use Auth\Service\AccessService;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Stdlib\Hydrator\ObjectProperty;
use Auth\Model\User;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Auth\View\Helper\UserInfo;
use Zend\ServiceManager\ServiceLocatorInterface;
use Auth\Model\AuthStorage;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface, ViewHelperProviderInterface
{
    private $whitelist = array(
        'Auth\Controller\Auth-login',
        'Auth\Controller\Auth-logout'
    );

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        
        $eventManager->attach(MvcEvent::EVENT_DISPATCH, array(
            $this,
            'boforeDispatch'
        ), 100);

        $eventManager->attach('dispatch', array($this, 'checkLogin'));

        // $eventManager->attachAggregate($e->getApplication()
        // ->getServiceManager()
        // ->get('Auth/Listener/AclListener'));
        return $this;
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

    public function checkLogin($e)
    {
        return;
        $accessService      = $e->getApplication()->getServiceManager()->get('AccessService');
        $target             = $e->getTarget();
        $match              = $e->getRouteMatch();
        $controller         = $match->getParam('controller');
        $action             = $match->getParam('action');
        $requestedResourse  = $controller . "-" . $action;

        if( !in_array($requestedResourse, $this->whitelist)){
            var_dump($accessService->allowed($controller, $action));
            if( !$accessService->allowed($controller, $action) ){
                return $target->redirect()->toUrl('/login');
            }
        }

    }

    /**
     * deprecated
     */
    public function boforeDispatch(MvcEvent $event)
    {
        return;
        
        $response = $event->getResponse();
        $controller = $event->getRouteMatch()->getParam('controller');
        $action = $event->getRouteMatch()->getParam('action');
        $requestedResourse = $controller . "-" . $action;
        $serviceManager = $event->getApplication()->getServiceManager();
        $accessService = $serviceManager->get('AccessService');
        $request = $event->getRequest();
        $target = $event->getTarget();
        if (! in_array($requestedResourse, $whiteList) || $requestedResourse != 'Auth\Controller\Success-index') {
            $status = $accessService->allowed($controller, $action);
            if (! $status) {
                //@todo add after login redirect
                $requestedUrl = "";
                if ($request->isPost()) {
                    //  redirect 2 HTTP_REFERER
                    $requestedUrl = $request->getHeader('referer')->getUri();
                } else {
                    //  redirect to requestet url
                    $requestedUrl = $request->getRequestUri();
                }
                $url = '/login';
               // $response->setHeaders($response->getHeaders()
               //     ->addHeaderLine('Location', $url));
               // $response->setStatusCode(302);
               // $response->sendHeaders();
                return $target->redirect()->toUrl('/login');
            }
        }
    }
}
