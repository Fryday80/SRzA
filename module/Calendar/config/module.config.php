<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Calendar\Controller\Calendar' => 'Calendar\Controller\CalendarController',
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'calendar' => __DIR__ . '/../view',
        ),
    ),
     'router' => array(
         'routes' => array(
             'calendar' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route'    => '/calendar',
                     'defaults' => array(
                         'controller' => 'Calendar\Controller\Calendar',
                         'action'     => 'index',
                     ),
                 ),
             ),
         ),
     ),
);
