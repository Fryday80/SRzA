<?php
namespace Equipment\Form\Filter;

use Zend\InputFilter\InputFilter;

class TentFilter extends InputFilter
{

    public function __construct($flag = null)
    {
        $this->commonFilter();
    }

    private function commonFilter(){

    }

    private function backendFilter()
    {
        //@todo
    }
}
