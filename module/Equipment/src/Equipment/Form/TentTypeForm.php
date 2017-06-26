<?php
namespace Equipment\Form;

use Equipment\Model\EnumTentShape;
use Equipment\Service\TentService;
use Zend\Form\Form;

class TentTypeForm extends Form
{
    /** @var TentService  */
    private $tentService;

    public function __construct(TentService $tentService)
    {
        $this->tentService = $tentService;
        parent::__construct("TentColors");
        $this->setAttribute('method', 'post');
        
        $this->add(array(
            'name' => 'id',
            'type' => 'hidden'
        ));
        // userId int
        $this->add(array(
            'name' => 'name',
            'type' => 'Text',
            'options' => array (
                'label' => 'Bezeichnung',
            ),
        ));
        $this->add(array(
            'name' => 'shape',
            'type' => 'Zend\Form\Element\Select',
            'options' => array (
                'label' => 'Form',
                'value_options' => $this->getShapesForSelect(),
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'id' => 'submitbutton',
            ),

        ));
    }
    
    private function getShapesForSelect()
    {
        $list = array();
        foreach (EnumTentShape::TRANSLATION as $key => $value) {
            $list[$key] = $value;
        }
        ksort($list);
        return $list;
    }
}