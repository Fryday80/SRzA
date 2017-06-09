<?php
namespace Auth\Form\Filter;

use Zend\InputFilter\InputFilter;

class EmailFilter extends InputFilter
{

    public function __construct()
    {
        $this->add(array(
            'name' => 'email',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'StripTags'
                ),
                array(
                    'name' => 'StringTrim'
                )
            ),
            'validators' => array(
                array(
                    'name' => 'EmailAddress',
                    'options' => array()
                )
            )
        ));
    }
}