<?php
namespace Auth\Form\Filter;

use Zend\InputFilter\InputFilter;

class UserFilter extends InputFilter
{

    public function __construct($filterFlag = null)
    {
        $this->add(array(
            'name' => 'id',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'Int'
                )
            )
        ));
        
        $this->add(array(
            'name' => 'email',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'StringTrim'
                ),
                array(
                    'name' => 'StripTags'
                ),
            ),
            'validators' => array(
                array(
                    'name' => 'EmailAddress',
                    'options' => array()
                )
            )
        ));
        $this->add(array(
            'name' => 'name',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'StringTrim',
                ),
                array(
                    'name' => 'StripTags'
                )
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 100
                    )
                )
            )
        ));
        $this->add(array(
            'name' => 'password',
            'required' => false,
            'filters' => array(
                array('name' => 'StringTrim')
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 4,
                        'max' => 32
                    )
                )
            )
        ));
        $this->add(array(
            'name' => 'passwordConfirm',
            'required' => false,
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
            'name' => 'birthday',
            'required' => false,
        ));
        $this->add(array(
            'name' => 'zip',
            'required' => false,
        ));
        if ($filterFlag !== null) $this->modify($filterFlag);
    }

    private function modify($filterFlag)
    {
        if ($filterFlag == 'adminedit'){
            
        }
    }
}
