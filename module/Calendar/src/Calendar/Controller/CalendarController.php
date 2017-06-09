<?php
namespace Calendar\Controller;

use Auth\Model\RoleTable;
use Auth\Service\AccessService;
use Calendar\Form\CalendarForm;
use Calendar\Form\EventForm;
use Calendar\Service\CalendarService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class CalendarController extends AbstractActionController
{
    /** @var CalendarService  */
    protected $calendarService;
    /** @var AccessService  */
    protected $accessService;
    /** @var RoleTable  */
    private $roleTable;

    function __construct(CalendarService $calendarService, AccessService $accessService, RoleTable $roleTable)
    {
        $this->calendarService = $calendarService;
        $this->accessService = $accessService;
        $this->roleTable = $roleTable;
    }

    public function indexAction()
    {
        $this->calendarService->getUpcoming();
        $form = new EventForm($this->calendarService);
        return new ViewModel(array(
            'calendars' => $this->calendarService->getCalendars(),
            'form' => $form,
            'canAdd' => $this->accessService->allowed('Calendar\Controller\Calendar', 'addEvent'),
            'canEdit' => $this->accessService->allowed('Calendar\Controller\Calendar', 'editEvent'),
            'canDelete' => $this->accessService->allowed('Calendar\Controller\Calendar', 'deleteEvent'),
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
        $results = $this->calendarService->getEventsFrom($post['start'], $post['end']);
        return new JsonModel($results);
    }
    public function configAction(){
        $calendarSet = array();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost()->toArray();
            $this->calendarService->setCalendarOverwrites($post);
            //redirect->calendar/config
        }
        $calendars = $this->calendarService->getCalendars();
        
        foreach ($calendars as $calendar ){
            $form = new CalendarForm($this->roleTable->getUserRoles());
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
