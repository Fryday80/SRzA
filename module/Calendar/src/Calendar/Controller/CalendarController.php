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
    public function indexAction()
    {
        /** @var CalendarService $calendarService */
        $calendarService = $this->getServiceLocator()->get("CalendarService");
        $calendarService->getUpcoming();
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

        $request = $this->getRequest();
        if (!$request->isPost()) {
            return new JsonModel(array(
                'error' => 'need post data startTime'
            ));
        }
        $post = $request->getPost();
        $calendarService = $this->getServiceLocator()->get("CalendarService");
        $results = $calendarService->getEventsFrom($post['start'], $post['end']);
        return new JsonModel($results);
    }
    public function configAction(){
        $calendarSet = array();
        /** @var CalendarService $calendarService */
        $calendarService = $this->getServiceLocator()->get('CalendarService');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost()->toArray();
            $calendarService->setCalendarOverwrites($post);
            //redirect->calendar/config
        }
        $calendars = $calendarService->getCalendars();

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
