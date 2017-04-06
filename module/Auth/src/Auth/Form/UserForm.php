<?php
namespace Auth\Form;

use Zend\Form\Form;
use Auth\Form\Filter\UserFilter;

class UserForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('User');
        $this->setAttribute('method', 'post');
        $this->setInputFilter(new UserFilter());

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));

        $this->add(array(
            'name' => 'email',
            'type' => 'Text',
            'options' => array(
                'label' => 'eMail',
                ),
            ),
            array(
                'priority' => 10, // Increase value to move to top of form
            ));

        $this->add(array(
            'name' => 'name',
            'type' => 'Text',
            'options' => array(
                'label' => 'Name',
                ),
            ),
            array(
                'priority' => 10, // Increase value to move to top of form
            ));
        
        $this->add(array(
            'name' => 'password',
            'type' => 'Password',
            'options' => array(
                'label' => 'Password',
                )
            ),
            array(
                'priority' => 2, // Increase value to move to top of form
            ));
        
        $this->add(array(
            'name'       => 'passwordConfirm',
            'type'       => 'Password',
            'options' => array(
                'label' => 'Password confirm',
                )
            ),
            array(
                'priority' => 2, // Increase value to move to top of form
            ));
        $this->add(array(
            'name' => 'status',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'Aktiv',
                ),
            ),
            array(
                'priority' => 11, // Increase value to move to top of form
            ));
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'id' => 'submitbutton',
                ),
            ),
            array(
                'priority' => 1, // Increase value to move to top of form
            ));
    }
}