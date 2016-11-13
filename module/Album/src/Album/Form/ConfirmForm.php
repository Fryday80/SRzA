<?php
namespace Album\Form;

use Zend\Form\Form;

class ConfirmForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('album');
        $this->setAttribute('action', '/album/delete');
        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'delete_confirm',
            'type' => 'Select',
            'attributes' => array (
                'id' => 'delete_confirm',
                'options' => array(
                    'no' => 'nicht Löschen',
                    'yes' => 'Löschen'
                )
            ),
            'options' => array(
                'label' => 'Wirklich das gesamte Album mit Bildern löschen?',
                'label_attributes' => array(
                    'class'  => ''
                ),
            )
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Löschen',
                'id' => 'submitbutton',
            ),
        ));
    }
}
