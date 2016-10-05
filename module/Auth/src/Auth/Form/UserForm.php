<?php
namespace Auth\Form;

use Zend\Form\Form;

class UserForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('user');

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
        ));
        
        $this->add(array(
            'name' => 'user_name',
            'type' => 'Text',
            'options' => array(
                'label' => 'Name',
            ),
        ));
        
        $this->add(array(
            'name' => 'password',
            'type' => 'Password',
            'required' => true,
            'options' => array(
                'label' => 'Password',
            ),
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim')
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 3,
                        'max' => 32
                    )
                )
            )
        ));
        
        $this->add(array(
            'name'       => 'passwordConfirm',
            'type'       => 'Password',
            'options' => array(
                'label' => 'Password confirm',
            ),
            'validators' => array(
                array(
                    'name'    => 'Identical',
                    'options' => array(
                        'token' => 'password',
                    ),
                ),
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