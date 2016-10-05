<?php
namespace Auth\Form\Filter;

use Zend\InputFilter\InputFilter;

class ResourceFilter extends InputFilter
{

    public function __construct()
    {
        $this->add(array(
            'name' => 'resource_name',
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
                        'max' => 50
                    )
                )
            )
        ));
        
        $this->add(array(
            'name' => 'permissions',
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
                        'max' => 500
                    )
                )
            )
        ));
    }
}