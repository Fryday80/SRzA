<?php
namespace Album\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

class Album
{

    public $id;

    public $artist;

    public $title;

    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (! empty($data['id'])) ? $data['id'] : null;
        $this->event = (! empty($data['event'])) ? $data['event'] : null;
        $this->datum = (! empty($data['datum'])) ? $data['datum'] : null;
        $this->duration = (! empty($data['duration'])) ? $data['duration'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (! $this->inputFilter) {
            $inputFilter = new InputFilter();
            
            $inputFilter->add(array(
                'name' => 'id',
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'Int'
                    )
                )
            ));
            
            $inputFilter->add(array(
                'name' => 'event',
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
                            'min' => 1,
                            'max' => 100
                        )
                    )
                )
            ));

            $inputFilter->add(array(               //datum und duration anders? weil datum und INT??
                'name' => 'datum',
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
                            'min' => 1,
                            'max' => 100
                        )
                    )
                )
            ));
            
            $inputFilter->add(array(
                'name' => 'duration',
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
                            'min' => 1,
                            'max' => 100
                        )
                    )
                )
            ));
            
            $this->inputFilter = $inputFilter;
        }
        
        return $this->inputFilter;
    }
}
