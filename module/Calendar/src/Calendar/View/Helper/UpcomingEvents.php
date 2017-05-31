<?php
namespace Calendar\View\Helper;

use Calendar\Service\CalendarService;
use Zend\View\Helper\AbstractHelper;
use Auth\Model\AuthStorage;

class UpcomingEvents extends AbstractHelper
{
    /**
     * @var AuthStorage
     */
    protected $storage;
    /**
     * @var CalendarService
     */
    protected $calendarService;

    public function __construct(AuthStorage $storage, CalendarService $calendarService) {
        $this->storage = $storage;
        $this->calendarService = $calendarService;
        return $this;
    }
    public function render(){
        $eventList = $this->calendarService->getUpcoming();
        foreach ($eventList as $event): ?>
            <a href="/calendar">
                <div>
                    <box class="upcoming">
                        <boxtitle class="upcoming">
                            <span class="own_text_small"><?php echo $event['title'] ?></span>
                        </boxtitle>
                        <boxcontent class="upcoming">
                            <?php echo date('D d.M.Y H:i', $event['startUnix']) ?>
                        </boxcontent>
                    </box>
                </div>
            </a>
        <?php endforeach;
    }
}
