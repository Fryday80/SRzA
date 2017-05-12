<?php
namespace Calendar\Controller;

use Calendar\Form\EventForm;
use Calendar\Service\CalendarService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class CalendarController extends AbstractActionController
{

    protected $albumTable;

    public function indexAction()
    {
        /** @var CalendarService $calendarService */
        $calendarService = $this->getServiceLocator()->get("CalendarService");

        $form = new EventForm($calendarService);
        return new ViewModel(array(
            'calendars' => $calendarService->getCalendars(),
            'form' => $form
        ));
    }
    public function getEventsAction() {

        $calendarService = $this->getServiceLocator()->get("CalendarService");
        $results = $calendarService->getEventsFrom(1,1);

        $items = $results->getItems();
        $result = [];
        bdump($items);
        foreach ($items as $value) {
            array_push($result, [
                'title'  => $value['summary'],
                'start'  => ($value['sequence'] == 3)? $value['start']['date'] : $value['start']['dateTime'],
                'end'    => ($value['sequence'] == 3)? $value['end']['date'] : $value['end']['dateTime'],
//                'id'     => $value['id'],
//                'allDay' => ($value['sequence'] == 3)? true: false,
//                'url' => 'leer',
//                'className' => [''],
//                'editable' => false,
                'startEditable' => true,
                'durationEditable' => true,
//                'source' => null,
//                'color' => '',
//                'backgroundColor' => '',
//                'borderColor' => '',
//                'textColor' => '',
            ]);
        }
        return new JsonModel($result);
//        return new JsonModel(array(
//            'id' => 42,
//            'title' => 'titel',
//            'allDay' => false,
//            'start' => 741269842,
//            'end' => 8524652,
//            'url' => 'leer',
//            'className' => [''],
//            'editable' => false,
//            'startEditable' => false,
//            'durationEditable' => false,
//            'source' => null,
//            'color' => '',
//            'backgroundColor' => '',
//            'borderColor' => '',
//            'textColor' => '',
//        ));
    }
    public function createEventAction() {
        //@todo implement
//        return new JsonModel(array(
//            'data' => 42
//        ));
    }
    public function removeEventAction() {
        //@todo implement
//        return new JsonModel(array(
//            'data' => 42
//        ));
    }
}
