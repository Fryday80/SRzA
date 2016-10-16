<?php
namespace Album\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

class Album
{

    public $id;

    public $event;

    public $date;

    public $timestamp;

    public $duration;

    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (! empty($data['id'])) ? $data['id'] : null;
        $this->event = (! empty($data['event'])) ? $data['event'] : null;
        $this->timestamp = (! empty($data['timestamp'])) ? $data['timestamp'] : null;
        $this->duration = (! empty($data['duration'])) ? $data['duration'] : null;
        $this->date = date('d-m-Y', $this->timestamp);
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

            $inputFilter->add(array(               //wird nicht gespeichert, sondern timestamp wird in db gespeichert
                'name' => 'date',
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'Int'
                    ),
                ),
            ));
            
            $inputFilter->add(array(
                'name' => 'duration',
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'Int'
                    ),
                ),
            ));
            
            $this->inputFilter = $inputFilter;
        }
        
        return $this->inputFilter;
    }
}
