<?php
namespace Auth\Form;

use Zend\Form\Form;
use Auth\Form\Filter\UserFilter;

class EmailForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('Email');
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'email',
            'type' => 'Text',
            'options' => array(
                'label' => 'eMail',
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Reset Password',
                'id' => 'submitbutton',
            ),
        ));
    }
}