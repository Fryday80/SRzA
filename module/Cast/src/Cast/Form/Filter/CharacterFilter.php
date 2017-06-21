<?php
namespace Cast\Form\Filter;

use Zend\InputFilter\InputFilter;

class CharacterFilter extends InputFilter
{

    public function __construct($flag = null)
    {
        $this->commonFilter();
        switch ($flag) {
            case 'backend':
                $this->backendFilter();
                break;
        }
    }

    private function commonFilter(){
        $this->add(array(
            'name' => 'id',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'Int',
                )
            )
        ));
        $this->add(array(
            'name' => 'birthday',
            'required' => false,
        ));
        $this->add(array(
            'name' => 'name',
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
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 3,
                        'max' => 20
                    )
                )
            )
        ));
        $this->add(array(
            'name' => 'surename',
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
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 3,
                        'max' => 20
                    )
                )
            )
        ));
        $this->add(array(
            'name' => 'user_id',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'Int'
                )
            )
        ));
        $this->add(array(
            'name' => 'family_id',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'Int'
                )
            )
        ));
        $this->add(array(
            'name' => 'guardian_id',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'Int'
                )
            )
        ));
        $this->add(array(
            'name' => 'tross_id',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'Int'
                )
            )
        ));
        $this->add(array(
            'name' => 'job_id',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'Int'
                )
            )
        ));
        $this->add(array(
            'name' => 'supervisor_id',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'Int'
                )
            )
        ));
        $this->add(array(
            'name' => 'active',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'Int'
                )
            )
        ));
    }

    private function backendFilter()
    {
        //@todo
    }
}
