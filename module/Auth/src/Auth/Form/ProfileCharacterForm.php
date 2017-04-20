<?php
namespace Auth\Form;

use Zend\Form\Form;

class ProfileCharacterForm extends Form
{
    public $familyList = array();

    public function __construct(Array $families)
    {
        $this->familyList = $families;

        parent::__construct("Character");
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'name',
            'type' => 'text',
            'options' => array(
                'label' => 'Vorname'
            )
        ));
        $this->add(array(
            'name' => 'surename',
            'type' => 'text',
            'options' => array(
                'label' => 'Name'
            )
        ));
        $this->add(array(
            'name' => 'user_id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'family_id',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
            ),
            'options' => array(
                'label' => 'Familie',
                'value_options' => $this->getFamiliesForSelect(),
            )
        ));
        $this->add(array(
            'name' => 'family_order',
            'type' => 'number',
            'options' => array(
                'label' => 'Familien Rang'
            )
        ));
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Submit',
            )
        ));
    }
    public function getFamiliesForSelect()
    {
        $selectData = array();
        foreach ($this->familyList as $fam) {
            $selectData[$fam['id']] = $fam['name'];
        }
        return $selectData;
    }
}