<?php
namespace Usermanager\Form;

use Zend\Form\Form;

class FamilyForm extends Form
{
    public function __construct()
    {
        parent::__construct("Family");
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'id', //db.user
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'family_id', //db.user
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'name',   //db.families
            'type' => 'text',
            'options' => array(
                'label' => 'Family Name'
            )
        ));
        $this->add(array(
            'name' => 'titel', //db.titel
            'type' => 'text',
            'options' => array(
                'label' => 'Titel'
            )
        ));
        $this->add(array(
            'name' => 'showname', //db.user
            'type' => 'text',
            'options' => array(
                'label' => 'Vorname (Darstellung)'
            )
        ));
        $this->add(array(
            'name' => 'showsurename', //db.user
            'type' => 'text',
            'options' => array(
                'label' => 'Nachname (Darstellung)'
            )
        ));
        $this->add(array(
            'name' => 'order', //db.user
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'vita', //db.user
            'type' => 'text',
            'options' => array(
                'label' => 'Vita (Darstellung)'
            )
        ));
        
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Submit',
            )
        ));
    }
}