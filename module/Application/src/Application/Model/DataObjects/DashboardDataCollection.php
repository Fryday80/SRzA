<?php
namespace Application\Model\DataObjects;

use Application\Service\StatisticService;

class DashboardDataCollection
{
    private$actionLog;
    private $activeUsers;
    private $systemLog;
    /** @var  $statsService StatisticService */
    private $statService;

    function __construct($sm)
    {
        $this->statService = $sm->get('StatisticService');
    }

    // getter
    public function getActiveUsers()
    {
        return (isset($this->activeUsers)) ? $this->activeUsers : $this->updateItem('activeUsers');
    }

    public function getActionLog()
    {
        return (isset($this->actionLog)) ? $this->actionLog : $this->updateItem('actionLog');
    }
    public function getSystemLog()
    {
        return (isset($this->systemLog)) ? $this->systemLog : $this->updateItem('systemLog');
    }

    // setter
    public function setActiveUsers( ActiveUsersSet $data = null )
    {
        $this->activeUsers = ($data == null) ? $this->updateItem('activeUsers') : $data;
    }

    public function setActionLog()
    {
        $this->actionLog = $this->updateItem('actionLog');
    }

    public function setSystemLog( SystemLogSet $data = null )
    {
        $this->actionLog = ($data == null) ? $this->updateItem('systemLog') : $data;
    }

    /**
     * @param string $item activeUsers | actionLog | systemLog | all
     * @param int $since UNIX timestamp of oldest entry
     * @return bool true if sucessfüll false on fail
     */
    public function update ($item = 'all')
    {
        switch ($item){
            case 'activeUsers':
                $this->activeUsers = $this->statService->getActiveUsers();
                break;
            case 'actionLog':
                $this->actionLog = $this->statService->getLastActions();
                break;
            case 'systemLog':
                $this->actionLog = $this->statService->getSystemLog();
                break;
            case 'all':
                $this->activeUsers = $this->statService->getActiveUsers();
                $this->actionLog = $this->statService->getLastActions();
                $this->actionLog = $this->statService->getSystemLog();
                break;
            default:
                trigger_error("string is no keyword known to DashboardData", E_USER_ERROR);
                return false;
                break;
        }
        return true;
    }

    /**
     * @param string $item activeUsers | actionLog | systemLog | all
     * @param int $since UNIX timestamp of oldest entry
     * @return ActionLogSet|ActiveUsersSet|SystemLogSet
     */
    private function updateItem($item)
    {
        switch ($item){
            case 'activeUsers':
                return $this->statService->getActiveUsers();
                break;
            case 'actionLog':
               return $this->statService->getLastActions();
                break;
            case 'systemLog':
               return $this->statService->getSystemLog();
                break;
        }
    }
}