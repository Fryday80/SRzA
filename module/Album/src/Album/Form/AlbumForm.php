<?php
namespace Album\Form;

use Zend\Form\Form;

class AlbumForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('album');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'event',
            'type' => 'Text',
            'options' => array(
                'label' => 'Event',
            ),
        ));
        $this->add(array(
            'name' => 'datum',
            'type' => 'Text',
            'options' => array(
                'label' => 'Datum',
            ),
        ));
        $this->add(array(
            'name' => 'duration',
            'type' => 'Text',
            'options' => array(
                'label' => 'Dauer',
            ),
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
}
