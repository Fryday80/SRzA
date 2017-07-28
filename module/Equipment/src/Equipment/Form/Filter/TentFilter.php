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

		$this->add(array(
			'name' => 'spareBeds',
			'required' => false,
		));
    }

    private function backendFilter()
    {
        //@todo
    }
}
