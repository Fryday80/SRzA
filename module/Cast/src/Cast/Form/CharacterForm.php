<?php
namespace Cast\Form;

use Zend\Form\Form;

class CharacterForm extends Form
{
    public $userList = array();
    public $familyList = array();

    public function __construct(Array $users, Array $families)
    {
        $this->userList = $users;
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
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
            ),
            'options' => array(
                'label' => 'Darsteller',
                'value_options' => $this->getUsersForSelect(),
            )
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
    public function getUsersForSelect()
    {
        $selectData = array();
        foreach ($this->userList as $user) {
            $selectData[$user['id']] = $user['name'];
        }
        return $selectData;
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