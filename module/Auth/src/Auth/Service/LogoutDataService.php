<?php

namespace Auth\Service;


use Application\Model\ActiveUser;
use Application\Service\StatisticService;

class LogoutDataService
{
    /** @var StatisticService $stats */
    private $stats;
    function __construct($sm)
    {
        $this->stats = $sm->get('StatisticService');
    }
    
    public function getLogoutData()
    {
        $activeUsers = $this->stats->getActiveUsers();
        $userListItems = array();
        /** @var ActiveUser $activeUser */
        foreach ($activeUsers as $activeUser) {
            array_push($userListItems, array(
                "name" => $activeUser->userName . '<img src="/img/uikit/led-green.png" class="active-user-icon">',
                "url" => "/profile/$activeUser->userName",
            ));
        }
        
        $logOutList = array(
            0 => array (
                "name" => "mein Menü",
                "class" => "my-menu",
                "list" => array (
                    0=> array(
                        "name" => "Mein Profil",
                        "url" => "#",
                    ),
                    1=> array(
                        "name" => "Meine Charaktere",
                        "url" => "#",
                    ),
                ),
            ),
            1 => array (
                "name" => "Active Users",
                "class" => "active-users",
                "list" => $userListItems,
            ),
        );
        return $logOutList;
    }
}