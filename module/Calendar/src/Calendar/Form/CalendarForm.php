<?php
namespace Calendar\Form;

use Calendar\Service\CalendarService;
use Zend\Form\Form;

class CalendarForm extends Form
{
    private $roles;

    public function __construct($roles)
    {

        $this->roles = $roles;
        parent::__construct('Calendar');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'summary',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'backgroundColor',
            'type' => 'Zend\Form\Element\Color',
            'required' => true,
            'options' => array(
                'label' => 'Color',
            )
        ));
        $this->add(array(
            'name' => 'role_id',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(),
            'options' => array(
                'label' => 'Minimum Role',
                'value_options' => $this->getRolesForSelect(),
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
    private function getRolesForSelect() {
        $selectData = array();
        foreach ($this->roles as $role) {
            $selectData[$role['rid']] = $role['role_name'];
        }
        return $selectData;
    }
}
