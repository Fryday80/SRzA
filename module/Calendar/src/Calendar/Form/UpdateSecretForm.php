<?php
namespace Calendar\Form;

use Zend\Form\Form;

class UpdateSecretForm extends Form
{

    public function __construct()
    {
        parent::__construct('UpdateToken');

        $this->add(array(
            'name' => 'newSecret',
            'type' => 'File',
            'options' => array(
                'label' => 'Secret',
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
