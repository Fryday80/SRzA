<?php
namespace Calendar\Form;

use Zend\Form\Form;

class CalendarSelectionForm extends Form
{
    public function __construct()
    {
        parent::__construct("CalendarSelectionForm");
        $this->setAttribute('method', 'post');

/// MultiCheckbox
        $this->add(array(
            'name' => 'months',
            'type' => 'Zend\Form\Element\MultiCheckbox',
            'options' => array(
                'label' => 'Monate',
                'value_options' => $this->getMonths(),
            ),
        ));

/// Submit
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'required' => true,
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Submit',
            )
        ));
    }

    private function getMonths(){
        $today = $this->today();
        $year = $today[2];
        $return[0] = 'Alle';
        $temp = array ("Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember");
        $months = array();
        $options = array();
        foreach ($temp as $key => $value){
            if ($key == 11){
                $months[$value] = $temp[0];
            }
            $months[$value] = $temp[$key+1];
        }
        for ($i=0; $i<12; $i++){
            if ($i == 0){
                array_push($options[$i], array($today[1], $year));
            } else {
                $month = $months[$options[$i-1]];
                if ($month == "Januar"){
                    $year++;
                    array_push($options[$i], array($month, $year));
                }
            }
        }
        foreach ($options as $option){
            array_push($return, $option[0] . ' ' . $option[1]);
        }
        return $return;
    }
    private function today(){
        return array(date("d"), date("m"), date("Y"));
    }
    private function thisYear(){
        $year = date("Y");
    }
    private function nextYear(){
        $year = date("Y")+1;
    }
}