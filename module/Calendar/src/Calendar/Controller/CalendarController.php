<?php
namespace Calendar\Controller;

use Calendar\Service\CalendarService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AlbumController extends AbstractActionController
{

    protected $albumTable;

    public function indexAction()
    {
        $calendarService = $this->getServiceLocator()->get("CalendarService");
        $results = $calendarService->getEventsFrom();

        if (count($results->getItems()) == 0) {
            print "No upcoming events found.\n";
        } else {
            print "Upcoming events:\n";
            foreach ($results->getItems() as $event) {
                $start = $event->start->dateTime;
                if (empty($start)) {
                    $start = $event->start->date;
                }
                printf("%s (%s)\n", $event->getSummary(), $start);
            }
        }
        return new ViewModel(array(
            'albums' => $this->getAlbumTable()->fetchAll()
        ));
    }
}
