<?php
namespace Album\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

class Album
{

    public $aid;    
    public $folder;
    public $event;
    public $timestamp;
    public $preview_pic;    
    public $avisibility;
    
    public $date;
    public $year;

    protected $inputFilter;

    public function exchangeArray($data)        //@todo wo wird das genutzt?
    {
        $this->aid = (! empty($data['aid'])) ? $data['aid'] : null;
        $this->folder (!empty ($data['folder'])) ? $data['folder'] : null;
        $this->event = (! empty($data['event'])) ? $data['event'] : null;
        $this->timestamp = (! empty($data['timestamp'])) ? $data['timestamp'] : null;
        $this->preview_pic = (! empty($data['preview_pic'])) ? $data['preview_pic'] : null;
        $this->date = date ('d.m.', $this->timestamp);
        $this->year = date ('Y', $this->timestamp);
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
                'name' => 'aid',
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'Int'
                    )
                )
            ));
            
            $inputFilter->add(array(
                'name' => 'folder',
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

            $inputFilter->add(array(
                'name' => 'timestamp',
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'Int'
                    ),
                ),
            ));

            $inputFilter->add(array(                //@todo er darf hier nicht bla.jpg rausschmeißen
                'name' => 'preview_pic',
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
                'name' => 'avisibilty',
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
