<?php
namespace Application\Form;

use Zend\Form\Form;

class MyForm extends Form
{
    public function __construct($formName = null, $options = null)
    {
        parent::__construct($formName, $options);
    }
    
    public function setData($data)
    {
        $data = $this->prepareDataForSetData($data);
        parent::setData($data);
    }
    
    protected function prepareDataForSetData ($data)
    {
        return $data;
    }
}