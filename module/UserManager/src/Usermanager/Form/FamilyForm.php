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
        $this->add(array(            //todo fry salt upload dependent select??? from defined folder?? js-show the sign?? dependent from rank 0 | 1
            'name' => 'sign',
            'type' => 'text',
            'attributes' => array(
                'label' => 'Wappen'
            )
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
            'name' => 'kids',
            'type' => 'Select',
            'attributes' => array (
                'label' => 'Anzahl Kinder',
                'class' => '',
                'options' => array(
                    0 => '--',
                    1 => '1',
                    2 => '2',
                    3 => '3',
                    4 => '4',
                    5 => '5',
                    6 => '6',
                    7 => '7',
                    8 => '8',
                ),
            ),
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