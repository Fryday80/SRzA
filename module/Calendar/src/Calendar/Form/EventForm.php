<?php
namespace Calendar\Form;

use Calendar\Service\CalendarService;
use Zend\Form\Form;

class EventForm extends Form
{
    /** @var CalendarService */
    private $calendarService;

    public function __construct($calendarService)
    {
        $this->calendarService = $calendarService;
        parent::__construct('Event');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'title',
            'type' => 'Text',
            'options' => array(
                'label' => 'Titel',
            ),
        ));
        $this->add(array(
            'name' => 'description',
            'type' => 'Text',
            'options' => array(
                'label' => 'Beschreibung',
            ),
        ));

        $this->add(array(
            'name' => 'startTime',
            'type' => 'Zend\Form\Element\DateTimeLocal',
            'options' => array(
                'label' => 'Start',
            ),
        ));
        $this->add(array(
            'name' => 'endTime',
            'type' => 'Zend\Form\Element\DateTimeLocal',
            'options' => array(
                'label' => 'End',
            ),
        ));
        $this->add(array(
            'name' => 'checkbox',
            'type' => 'Zend\Form\Element\Checkbox',
            'options' => array(
                'label' => 'AllDay',
            ),
        ));
        $this->add(array(
            'name' => 'calendar_id',
            'type' => 'Zend\Form\Element\Select',
            'required' => true,
            'options' => array(
                'label' => 'Kalender',
                'value_options' => $this->createCalendarList(),
            )
        ));
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'id' => 'submitbutton',
            ),
        ));
    }
    private function createCalendarList() {
        $list = $this->calendarService->getCalendars();
        $result = [];
        foreach ($list as $calendar) {
            $result[$calendar['id']] = $calendar['summary'];
        }
        return $result;
    }
}
