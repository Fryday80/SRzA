<?php
namespace Auth\Form\Filter;

use Zend\InputFilter\InputFilter;

class RoleFilter extends InputFilter
{

    public function __construct()
    {
        $this->add(array(
            'name' => 'role_name',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim')
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 45
                    )
                )
            )
        ));
        
        $this->add(array(
            'name' => 'role_parent',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim')
            )
        ));
    }
}