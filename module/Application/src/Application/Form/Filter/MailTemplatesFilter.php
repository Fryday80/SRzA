<?php
namespace Application\Form\Filter;

use Zend\InputFilter\InputFilter;

class MailTemplatesFilter extends InputFilter
{

    public function __construct($filterFlag = null)
    {
        $this->add(array(
            'name' => 'name',
            'required' => true,
        ));
        $this->add(array(
            'name' => 'sender',
            'required' => true,
        ));
        $this->add(array(
            'name' => 'subject',
            'required' => true,
        ));
        $this->add(array(
            'name' => 'msg',
            'required' => true,
        ));
    }
}
