<?php
namespace Auth;

use Application\Model\AbstractModels\TimeLog;
use Application\Model\Enums\ActionType;
use Application\Model\Enums\HitType;
use Application\Model\Enums\LogType;
use Application\Model\DataModels\Action;
use Application\Model\DataModels\SystemLog;
use Application\Service\StatisticService;
use Auth\Model\User;
use Auth\Model\Tables\UserTable;
use Auth\Model\Tables\RoleTable;
use Auth\Model\Tables\PermissionTable;
use Auth\Model\Tables\ResourceTable;
use Auth\Model\Tables\RolePermissionTable;
use Auth\Model\Tables\UserRoleTable;
use Auth\Model\AuthStorage;
use Auth\Service\AccessService;
use Auth\Service\UserService;
use Auth\View\Helper\LoginView;
use Auth\View\Helper\UserInfo;
use Zarganwar\PerformancePanel\Register;
use Zend\Http\Request;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\Controller\Plugin\FlashMessenger;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\ModuleRouteListener;
use Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Stdlib\Hydrator\ObjectProperty;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\Navigation\AbstractHelper;

class Module
{
    private $whitelist = array(
        'Auth\Controller\Auth-login',
        'Auth\Controller\Auth-logout',
        'Auth\Controller\Auth-reset',
        'Auth\Controller\Auth-resetRequest',
        'Application\Controller\System-maintenance',
        'Application\Controller\System-message',
    );

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        
        $eventManager->attach('dispatch', array($this, 'checkLogin'));
        return $this;
    }

    public function checkLogin(MvcEvent $e)
    {
        return;
        /** @var Request $request */
        $serviceManager     = $e->getApplication()->getServiceManager();
        /** @var AccessService $accessService */
        $accessService      = $serviceManager->get('AccessService');
        /** @var StatisticService $statsService */
        $statsService       = $serviceManager->get('StatisticService');
        $request            = $e->getRequest();
        $clientIP           = $request->getServer('REMOTE_ADDR');
        $target             = $e->getTarget();
        $match              = $e->getRouteMatch();
        $controller         = $match->getParam('controller');
        $action             = $match->getParam('action');
        $title              = $match->getParam('title');
        $requestedResourse  = $controller . '-' . $action;
        /** @var FlashMessenger $flashMessenger */
        $flashMessenger     = $e->getApplication()-> getServiceManager()->get('controllerpluginmanager')->get('flashmessenger');

        if ($action != 'logout' && $accessService->hasIdentity() && $clientIP != $accessService->getUserIP()) {
            //SID hijacked log as website event and logout
            return $target->redirect()->toUrl('/logout');
        }
        if( !in_array($requestedResourse, $this->whitelist)){
            if( !$accessService->allowed($controller, $action) ){
            	$userId   = $accessService->getUserID();
            	$userName = $accessService->getUserName();
            	$mTime = microtime(true);
                $statsService->logAction(new Action(
					$mTime,
					"$requestedResourse",
					"$userId",
					"$userName",
					ActionType::NOT_ALLOWED,
					"$title",
					'Access not allowed',
					null
				));
                //log to stats
                $hitType = ( $accessService->hasIdentity() )? HitType::MEMBER : HitType::GUEST;
                $statsService->logSystem($test = new SystemLog(
                    $mTime,
                    ( $hitType == HitType::MEMBER ) ? LogType::ERROR_MEMBER : LogType::ERROR_GUEST, //type
                    'Acess not allowed',
					$request->getUriString(),
					"$userId",
					"$userName",
					$request
                ));
                bdump($test);
                if ($request->isXmlHttpRequest()) {
                    $e->getResponse()->setStatusCode(403);
                    echo json_encode(['error' => true, 'message' => 'Not Allowed', 'code' => 403]);
                    die;
                } else {
                    $flashMessenger->addMessage($request->getUriString(), 'referer', 1);
                    return $target->redirect()->toUrl('/login');
                }
            }
        }
        AbstractHelper::setDefaultAcl($accessService->getAcl());
        AbstractHelper::setDefaultRole($accessService->getRole());
    }
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
//            'Zend\Loader\StandardAutoloader' => array(
//                'namespaces' => array(
//                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
//                )
//            )
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
}
