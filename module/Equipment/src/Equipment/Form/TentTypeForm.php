<?php
namespace Equipment\Form;

use Equipment\Model\EnumTentShape;
use Equipment\Service\TentService;
use Zend\Form\Form;

class TentTypeForm extends Form
{
    /** @var TentService  */
    private $tentService;

    public function __construct()
    {
        parent::__construct("TentTypes");
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
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'id' => 'submitbutton',
            ),

        ));
    }
}