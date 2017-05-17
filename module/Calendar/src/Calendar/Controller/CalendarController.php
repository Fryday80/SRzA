<?php
namespace Calendar\Controller;

use Calendar\Form\CalendarForm;
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
        $accessService = $this->getServiceLocator()->get('AccessService');
        $form = new EventForm($calendarService);
        return new ViewModel(array(
            'calendars' => $calendarService->getCalendars(),
            'form' => $form,
            'canAdd' => $accessService->allowed('Calendar\Controller\Calendar', 'addEvent'),
            'canEdit' => $accessService->allowed('Calendar\Controller\Calendar', 'editEvent'),
            'canDelete' => $accessService->allowed('Calendar\Controller\Calendar', 'deleteEvent'),
        ));
    }
    public function getEventsAction() {

        $calendarService = $this->getServiceLocator()->get("CalendarService");
        $results = $calendarService->getEventsFrom(1,1);

        $items = $results->getItems();
        $result = [];
//        bdump($items);die;
        foreach ($items as $value) {
            array_push($result, [
                'title'  => $value['summary'],
                'start'  => ($value['sequence'] == 3)? $value['start']['date'] : $value['start']['dateTime'],
                'end'    => ($value['sequence'] == 3)? $value['end']['date'] : $value['end']['dateTime'],
//                'id'     => $value['id'],
                'description' => $value['description'],
                'allDay' => ($value['sequence'] == 3)? true: false,
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
    public function configAction(){
        $calendarSet = array();
        /** @var CalendarService $calendarService */
        $calendarService = $this->getServiceLocator()->get('CalendarService');
        $calendars = $calendarService->getCalendars();
        bdump($calendars);
        $roleTable = $this->getServiceLocator()->get("Auth\Model\RoleTable");
        $allRoles = $roleTable->getUserRoles();
//        $form = new CalendarForm($allRoles);
        foreach ($calendars as $calendar ){
            $form = new CalendarForm($allRoles);
            $form->setData($calendar);
            array_push($calendarSet, $form);
        }
        return new ViewModel(array(
            'calendars' => $calendars,
            'calendarSet' => $calendarSet
        ));
    }
    public function addEventAction() {
        $request = json_decode($this->getRequest()->getContent());
        //@todo implement
        return new JsonModel(array(
            'data' => 'add newEvent triggered',
            'request' => $request
        ));
    }
    public function editEventAction() {
        $request = json_decode($this->getRequest()->getContent());
        //@todo implement
        // save ($request->title, $request->description ....)
        return new JsonModel(array(
            'data' => 'edit triggered',
            'request' => $request
        ));
    }
    public function deleteEventAction() {
        $request = json_decode($this->getRequest()->getContent());
        var_dump($request);
        //@todo implement
        return new JsonModel(array(
            'data' => 'delete triggered',
            'request' => $request
        ));
    }
}
