<?php
return array(
    'controllers' => array(
        'factories' => array(
            'Calendar\Controller\Calendar' => '\Calendar\Factory\Controller\CalendarControllerFactory',
        ),
    ),
    'service_manager' => array(
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
        'invokables' => array(),
        'factories' => array(
            'CalendarService' => '\Calendar\Factory\Service\CalendarServiceFactory',
        ),
        'abstract_factories' => array( )
    ),
    'view_helpers' => array(
        'invokables' => array(),
        'factories' => array(
            'upcoming' => '\Calendar\Factory\Helper\UpcomingEventsFactory',
        )
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

             'getevents' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route'    => '/calendar/getEvents',
                     'defaults' => array(
                         'controller' => 'Calendar\Controller\Calendar',
                         'action'     => 'getEvents',
                     ),
                 ),
             ),
             'config' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route'    => '/calendar/config',
                     'defaults' => array(
                         'controller' => 'Calendar\Controller\Calendar',
                         'action'     => 'config',
                     ),
                 ),
             ),
             'addEvent' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route'    => '/calendar/addEvent',
                     'defaults' => array(
                         'controller' => 'Calendar\Controller\Calendar',
                         'action'     => 'addEvent',
                     ),
                 ),
             ),
             'editEvent' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route'    => '/calendar/editEvent',
                     'defaults' => array(
                         'controller' => 'Calendar\Controller\Calendar',
                         'action'     => 'editEvent',
                     ),
                 ),
             ),
             'deleteEvent' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route'    => '/calendar/deleteEvent',
                     'defaults' => array(
                         'controller' => 'Calendar\Controller\Calendar',
                         'action'     => 'deleteEvent',
                     ),
                 ),
             ),

         ),
     ),
);
