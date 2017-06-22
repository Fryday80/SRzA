<?php
namespace Auth;

use Application\Model\Abstracts\ActionType;
use Application\Model\Abstracts\HitType;
use Application\Model\Abstracts\LogType;
use Application\Model\DataObjects\Action;
use Application\Model\DataObjects\SystemLog;
use Application\Service\StatisticService;
use Auth\Model\User;
use Auth\Model\UserTable;
use Auth\Model\RoleTable;
use Auth\Model\PermissionTable;
use Auth\Model\ResourceTable;
use Auth\Model\RolePermissionTable;
use Auth\Model\UserRoleTable;
use Auth\Model\AuthStorage;
use Auth\Service\AccessService;
use Auth\Service\UserService;
use Auth\View\Helper\LoginView;
use Auth\View\Helper\UserInfo;
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
        /** @var FlashMessenger $flashmessanger */
        $flashmessanger     = $e->getApplication()-> getServiceManager()->get('controllerpluginmanager')->get('flashmessenger');

        if ($action != 'logout' && $accessService->hasIdentity() && $clientIP != $accessService->getUserIP()) {
            //SID hijacked log as website event and logout
            return $target->redirect()->toUrl('/logout');
        }
        if( !in_array($requestedResourse, $this->whitelist)){
            if( !$accessService->allowed($controller, $action) ){
                $action = new Action(
                    microtime(),
                    $requestedResourse,
                    $accessService->getUserID(),
                    $accessService->getUserName(),
                    ActionType::NOT_ALLOWED,
                    $title,
                    'access not allowed',
                    $data = null
                );
                $statsService->logAction($action);
                //log to stats
                $hitType = ( $accessService->hasIdentity() )? HitType::MEMBER : HitType::GUEST;
                $statsService->logSystem(new SystemLog(
                    time(), //mTime
                    ( $hitType == HitType::MEMBER ) ? LogType::ERROR_MEMBER : LogType::ERROR_GUEST, //type
                    $title,       //msg
                    $request,   //url
                    $accessService->getUserID(),    //UserID
                    $accessService->getUserName(),  //userName
                    $data = null    //data
                ));
                if ($request->isXmlHttpRequest()) {
                    $e->getResponse()->setStatusCode(403);
                    echo json_encode(['error' => true, 'message' => 'Not Allowed', 'code' => 403]);
                    die;
                } else {
                    $flashmessanger->addMessage($request->getUriString(), 'referer', 1);
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
