<?php
namespace Auth;

return array(
    'controllers' => array(
        'invokables' => array(
            'Auth\Controller\User'          => 'Auth\Controller\UserController',
            'Auth\Controller\Auth'          => 'Auth\Controller\AuthController',
            'Auth\Controller\Role'          => 'Auth\Controller\RoleController',
            'Auth\Controller\Permission'    => 'Auth\Controller\PermissionController',
            'Auth\Controller\Resource'      => 'Auth\Controller\ResourceController',
            'Auth\Controller\Profile'       => 'Auth\Controller\ProfileController'
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
                        'action' => 'reset'
                    )
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
                        'action' => 'index',
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
                                'action' => 'index',
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
