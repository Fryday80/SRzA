<?php
namespace Auth;

use Application\Factory\Basic\DefaultTableGatewayFactory;

return array(
    'controllers' => array(
        'factories' => array(
            'Auth\Controller\User'       => 'Auth\Factory\Controller\UserControllerFactory',
            'Auth\Controller\Auth'       => 'Auth\Factory\Controller\AuthControllerFactory',
            'Auth\Controller\Role'       => 'Auth\Factory\Controller\RoleControllerFactory',
            'Auth\Controller\Permission' => 'Auth\Factory\Controller\PermissionControllerFactory',
            'Auth\Controller\Resource'   => 'Auth\Factory\Controller\ResourceControllerFactory',
            'Auth\Controller\Profile'    => 'Auth\Factory\Controller\ProfileControllerFactory',
        ),
        'invokables' => array( )
    ),
    'service_manager' => array(
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
        'invokables' => array(),
        'factories' => array(
            'AccessService'          => 'Auth\Factory\Service\AccessServiceFactory',
            'Auth\AclService'        => 'Auth\Factory\Service\AclServiceFactory',
            'AuthService'            => 'Auth\Factory\Service\AuthServiceFactory',
            'Auth\Model\AuthStorage' => 'Auth\Factory\AuthStorageFactory',
            'Auth\Model\RoleTable'   => 'Auth\Factory\Table\RoleTableFactory',
            'Auth\Model\UserTable'   => 'Auth\Factory\Table\UserTableFactory',
            'UserService'            => 'Auth\Factory\Service\UserServiceFactory'
        ),
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
            'Auth\Model\PermissionTable'     => DefaultTableGatewayFactory::class,
            'Auth\Model\ResourceTable'       => DefaultTableGatewayFactory::class,
            'Auth\Model\RolePermissionTable' => DefaultTableGatewayFactory::class,
        )
    ),
    'view_helpers' => array(
        'invokables' => array(),
        'factories' => array(
            'loginview' => 'Auth\Factory\Helper\LoginViewFactory',
            'userinfo'  => 'Auth\Factory\Helper\UserInfoFactory',
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'auth' => __DIR__ . '/../view'
        )
    ),
    'router' => array(
        'routes' => array(
            'user' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/user',
                    'constraints' => array(),
                    'defaults' => array(
                        'controller' => 'Auth\Controller\User',
                        'action' => 'index'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'delete' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/delete[/:id]',
                            'constraints' => array(
                                'id' => '[0-9]+'
                            ),
                            'defaults' => array(
                                'action' => 'delete',
                                'id' => '[0-9]+'
                            )
                        )
                    ),
                    'add' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/add[/:id]',
                            'constraints' => array(
                                'id' => '[0-9]+'
                            ),
                            'defaults' => array(
                                'action' => 'add',
                                'id' => '[0-9]+'
                            )
                        )
                    ),
                    'edit' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/edit[/:id]',
                            'constraints' => array(
                                'id' => '[0-9]+'
                            ),
                            'defaults' => array(
                                'action' => 'edit',
                                'id' => '[0-9]+'
                            )
                        )
                    )
                )
            ),
            'login' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/login',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Auth\Controller',
                        'controller' => 'Auth',
                        'action' => 'login'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'process' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:action]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*'
                            ),
                            'defaults' => array()
                        )
                    )
                )
            ),
            'logout' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/logout',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Auth\Controller',
                        'controller' => 'Auth',
                        'action' => 'logout'
                    )
                )
            ),
            'success' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/success',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Auth\Controller',
                        'controller' => 'Auth',
                        'action' => 'success'
                    )
                )
            ),
            'role' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/role',
                    'defaults' => array(
                        'controller' => 'Auth\Controller\Role',
                        'action' => 'index'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'delete' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/delete/:id',
                            'constraints' => array(
                                'id' => '[0-9]+'
                            ),
                            'defaults' => array(
                                'action' => 'delete',
                                'id' => '[0-9]+'
                            )
                        )
                    ),
                    'add' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/add[/:id]',
                            'constraints' => array(
                                'id' => '[0-9]+'
                            ),
                            'defaults' => array(
                                'action' => 'add',
                                'id' => '[0-9]+'
                            )
                        )
                    ),
                    'edit' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/edit[/:id]',
                            'constraints' => array(
                                'id' => '[0-9]+'
                            ),
                            'defaults' => array(
                                'action' => 'edit',
                                'id' => '[0-9]+'
                            )
                        )
                    )
                )
            ),
            'permission' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/permission',
                    'defaults' => array(
                        'controller' => 'Auth\Controller\Permission',
                        'action' => 'index'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'delete' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/delete[/:id]',
                            'constraints' => array(
                                'id' => '[0-9]+'
                            ),
                            'defaults' => array(
                                'action' => 'delete',
                                'id' => '[0-9]+'
                            )
                        )
                    ),
                    'add' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/add[/:id]',
                            'constraints' => array(
                                'id' => '[0-9]+'
                            ),
                            'defaults' => array(
                                'action' => 'add',
                                'id' => '[0-9]+'
                            )
                        )
                    ),
                    'edit' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/edit[/:id]',
                            'constraints' => array(
                                'id' => '[0-9]+'
                            ),
                            'defaults' => array(
                                'action' => 'edit',
                                'id' => '[0-9]+'
                            )
                        )
                    )
                )
            ),
            'resource' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/resource[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'Auth\Controller\Resource',
                        'action' => 'index'
                    )
                )
            ),
            'register' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/register',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Auth\Controller',
                        'controller' => 'Auth',
                        'action' => 'register'
                    )
                )
            ),
            'passwordReset' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/password/reset',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Auth\Controller',
                        'controller' => 'Auth',
                        'action' => 'resetRequest'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'hash' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/:hash',
//                            'constraints' => array(
//                                'hash' => '[0-9]+'
//                            ),
                            'defaults' => array(
                                'action' => 'reset',
//                                'hash' => '[0-9]+'
                            )
                        )
                    ),
                )
            ),
            'profileJson' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/profileJson',
                    'constraints' => array(),
                    'defaults' => array(
                        'controller' => 'Auth\Controller\Profile',
                        'action' => 'json',
                    )
                )
            ),
            'profile' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/profile',
                    'defaults' => array(
                        'controller' => 'Auth\Controller\Profile',
                        'action' => 'privateProfile',
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'username' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/:username',
                            'constraints' => array(
                                'username' => '[a-zA-Z][a-zA-Z0-9_-]+'
                            ),
                            'defaults' => array(
                                'action' => 'publicProfile',
                            )
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'charname' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:charname',
                                    'constraints' => array(
                                        'charname' => '[a-zA-Z][a-zA-Z0-9_-]+'
                                    ),
                                    'defaults' => array(
                                        'action' => 'charprofile',
                                    )
                                )
                            )
                        )
                    )
                )
            ),
        )
    )
);
