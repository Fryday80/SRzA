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
