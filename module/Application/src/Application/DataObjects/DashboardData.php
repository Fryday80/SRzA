<?php
namespace Application\DataObjects;

use Application\Service\StatisticService;
use Application\DataObjects\ActiveUsers;

class DashboardData
{
    private $actionLog;
    private $activeUsers;
    /** @var  $statsService StatisticService */
    private $statService;

    function __construct($sm)
    {
        $this->statService = $sm->get('StatisticService');
    }

    public function getActiveUsers()
    {
        if (!isset($this->activeUsers)){
            $this->activeUsers = $this->updateItem('activeUsers');
        }
        return $this->activeUsers;
    }

    /**
     * @return array array of Action Objects
     */
    public function getActionLog()
    {
        if (!$this->actionLog) {
            $this->actionLog = $this->updateItem('actionLog');
        }
        return $this->actionLog;
    }

    public function setActiveUsers( $data = null )
    {
        $this->activeUsers = ($data == null) ? $this->updateItem('activeUsers') : $data;
    }
    public function setActionLog( $data = null )
    {
        $this->actionLog = ($data == null) ? $this->updateItem('actionLog') : $data;
    }

    /**
     * @param null $item
     */
    public function update ($item = null)
    {
        switch ($item){
            case 'activeUsers':
                $this->activeUsers = $this->statService->getActiveUsers();
                break;
            case 'actionLog':
                $this->actionLog = $this->statService->getLastActions();
                break;
            case null:
                $this->activeUsers = $this->statService->getActiveUsers();
                $this->actionLog = $this->statService->getLastActions();
                break;
            default:
                return trigger_error("string is no keyword known to DashboardData", E_USER_ERROR);
                break;
        }
    }
    private function updateItem($item)
    {
        switch ($item){
            case 'activeUsers':
                return $this->statService->getActiveUsers();
                break;
            case 'actionLog':
               return $this->statService->getLastActions();
                break;
        }
    }
}