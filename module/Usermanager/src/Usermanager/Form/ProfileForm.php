<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 16.11.2016
 * Time: 12:47
 */


namespace Usermanager\Form;

use Zend\Form\Form;

Class ProfileForm extends Form
{
    public function __construct($accessService, $owner = false)
    {
        /* @var $accessService \Auth\Service\AccessService */

        parent::__construct('Profile');

        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'name',
            'type' => 'text',
            'attributes' => array(
                'readonly' => "TRUE",
            ),
            'options' => array (
                'label' => 'Benutzername'
            ),
        ));
        $this->add(array(
            'name' => 'membernumber',
            'type' => 'Text',
            'attributes' => array(
                'readonly' => "TRUE",
            ),
            'options' => array(
                'label' => 'Mitgliedsnummer',
            ),
        ));
        $this->add(array(
            'name' => 'email',
            'type' => 'email',
            'attributes' => array(
                'readonly' => "TRUE",
            ),
            'options' => array (
                'label' => 'eMail'
            ),
        ));
        $this->add(array(
            'name' => 'birthday',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'birthday_dp',
            'type' => 'Text',
            'attributes' => array(
                'readonly' => "TRUE",
            ),
            'options' => array (
                'label' => 'Geburtstag'
            ),
        ));
        $this->add(array(
            'name' => 'gender',
            'type' => 'Text',
            'attributes' => array(
                'readonly' => "TRUE",
            ),
            'options' => array (
                'label' => 'Geschlecht',
            ),
        ));
        $this->add(array(
            'name' => 'role_name',
            'type' => 'text',
            'attributes' => array(
                'readonly' => "TRUE",
            ),
            'options' => array (
                'label' => 'Rolle',
            ),
        ));
        $this->add(array(
            'name' => 'created_on',
            'type' => 'Text',
            'attributes' => array(
                'readonly' => "TRUE",
            ),
            'options' => array (
                'label' => 'Anmeldedatum',
            ),
        ));
        $this->add(array(
            'name' => 'modified_on',
            'type' => 'Text',
            'attributes' => array(
                'readonly' => "TRUE",
            ),
            'options' => array (
                'label' => 'Letzte Änderung',
            ),
        ));
        $this->add(array(
            'name' => 'realfirstname',
            'type' => 'text',
            'attributes' => array(
                'readonly' => "TRUE",
            ),
            'options' => array (
                'label' => 'Vorname'
            ),
        ));
        $this->add(array(
            'name' => 'realname',
            'type' => 'text',
            'attributes' => array(
                'readonly' => "TRUE",
            ),
            'options' => array (
                'label' => 'Name'
            ),
        ));
        $this->add(array(
            'name' => 'street',
            'type' => 'text',
            'attributes' => array(
                'readonly' => "TRUE",
            ),
            'options' => array (
                'label' => 'Strasse'
            ),
        ));
        $this->add(array(
            'name' => 'zip',
            'type' => 'text',
            'attributes' => array(
                'readonly' => "TRUE",
            ),
            'options' => array (
                'label' => 'PLZ'
            ),
        ));
        $this->add(array(
            'name' => 'city',
            'type' => 'text',
            'attributes' => array(
                'readonly' => "TRUE",
            ),
            'options' => array (
                'label' => 'Ort'
            ),
        ));

        if ($owner)
        {
            $this->ownerFieldset();
        }
        //only with permission
        if ($accessService->allowed("Usermanger\Controller\UsermanagerController", "edit"))
        {
            $this->editFieldset();
        }

//fry cash 'n' Carry^^ other tables???
    }


    private function ownerFieldset ()
    {
        $elements = $this->getElements();
        foreach ($elements as $element)
        {
            if (!in_array($element->getAttribute('name'), array( 'membernumber' , 'role_name', 'created_on', 'modified_on' )))
            {
                $element->removeAttributes(array('readonly'));
            }
        }
        $this->changeBoth();
    }

    
    private function editFieldset ()
    {
        $elements = $this->getElements();
        foreach ($elements as $element)
        {
            if (!in_array($element->getAttribute('name'), array( 'created_on', 'modified_on' )))
            {
                $element->removeAttributes(array('readonly'));
            }
        }
        $this->add(array(
            'name' => 'status',
            'type' => 'Select',
            'attributes' => array(
                'options' => array(
                    'Y' => 'active',
                    'N' => 'inactive'
                ),
            ),
            'options' => array(
                'label' => 'Status',
            ),
        ));
        $this->add(array(
            'name' => 'role_name',
            'type' => 'Select',
            'attributes' => array(
                'options' => array(
                    'Probemitglied'     => 'Probemitglied',
                    'Mitglied'          => 'Mitglied',
                    'Abteilungsleitung' => 'Abteilungsleitung'
                ),
            ),
            'options' => array(
                'label' => 'Rolle',
            ),
        ));
        $this->changeBoth();
        $this->addDeleteSubmit();
    }


    private function changeBoth (){
        $this->add(array(
            'name' => 'gender',
            'type' => 'Select',
            'attributes' => array(
                'options' => array(
                    'm' => 'Mann',
                    'f' => 'Frau'
                ),
            ),
            'options' => array (
                'label' => 'Geschlecht',
            ),
        ));
        $this->addChangeSubmit();
    }
    private function addChangeSubmit(){
        $this->add(array(
            'name' => 'change',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Änderungen speichern',
            ),
        ));
    }

    private function addDeleteSubmit(){
        $this->add(array(
            'name' => 'delete',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Löschen',
            ),
        ));
    }
    
    private function roleNameOptions($accessService){
        $this
    }
}