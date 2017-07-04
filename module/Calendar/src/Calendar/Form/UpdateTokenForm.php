<?php
namespace Calendar\Form;

use Zend\Form\Form;

class UpdateTokenForm extends Form
{

    public function __construct()
    {
        parent::__construct('UpdateToken');

        $this->add(array(
            'name' => 'newToken',
            'type' => 'Textarea',
            'options' => array(
                'label' => 'Token',
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
