<?php
namespace Calendar\Service;

use Auth\Service\AccessService;
use Calendar\DataTable\CalendarTable;
use Zarganwar\PerformancePanel\Register;

/** "true" logs speed in Tracy "false" don't */
const SPEED_CHECK = true;

class CalendarService
{
    /** @var  AccessService */
    private $accessService;
    /** @var  CalendarTable */
    private $calendar;

    function __construct($sm)
    {
        if (SPEED_CHECK) Register::add('CalendarService start');
        
        $this->accessService = $sm->get('AccessService');
        $this->calendar = $this->sm->get('CalendarService');

        if (SPEED_CHECK) Register::add('CalendarService constructed');
    }

    public function getAllAppointments(){
        return $this->calendar->getAll();
    }

    public function getAllActiveAppointments(){
        return $this->calendar->getAllActive();
    }
    
    public function getAllInactiveAppointments(){
        return $this->calendar->getAllInactive();
    }
}