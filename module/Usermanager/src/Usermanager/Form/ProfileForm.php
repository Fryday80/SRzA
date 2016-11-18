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
    public function __construct()
    {
        parent::__construct('Profile');
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'membernumber',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'read_only',
                'readonly' => "TRUE",
            ),
            'options' => array (
                'label' => 'Mitgliedsnummer',
            ),
        ));
        $this->add(array(
            'name' => 'name',
            'type' => 'text',
            'attributes' => array(
            ),
            'options' => array (
                'label' => 'Benutzername'
            ),
        ));
        $this->add(array(
            'name' => 'email',
            'type' => 'email',
            'attributes' => array(
            ),
            'options' => array (
                'label' => 'eMail'
            ),
        ));
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
        $this->add(array(
            'name' => 'status',
            'type' => 'Select',
            'attributes' => array(
                'class' => 'read_only',
                'class' => 'editor',
                'options' => array(
                    'Y' => 'active',
                    'N' => 'inactive'
                ),
            ),
            'options' => array (
                'label' => 'Status',
            ),
        ));
        $this->add(array(
            'name' => 'role_name',
            'type' => 'text',
            'attributes' => array(
                'class' => 'read_only',
                'class' => 'editor',
            ),
            'options' => array (
                'label' => 'Rolle',
            ),
        ));
        $this->add(array(
            'name' => 'created_on',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'read_only',
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
                'class' => 'read_only',
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
            ),
            'options' => array (
                'label' => 'Vorname'
            ),
        ));
        $this->add(array(
            'name' => 'realname',
            'type' => 'text',
            'attributes' => array(
            ),
            'options' => array (
                'label' => 'Name'
            ),
        ));
        $this->add(array(
            'name' => 'street',
            'type' => 'text',
            'attributes' => array(
            ),
            'options' => array (
                'label' => 'Strasse'
            ),
        ));
        $this->add(array(
            'name' => 'zip',
            'type' => 'text',
            'attributes' => array(
            ),
            'options' => array (
                'label' => 'PLZ'
            ),
        ));
        $this->add(array(
            'name' => 'city',
            'type' => 'text',
            'attributes' => array(
            ),
            'options' => array (
                'label' => 'Ort'
            ),
        ));

        //fry cash 'n' Carry^^ other tables
        //Submitbuttons
        $this->add(array(
            'name' => 'go_to_show',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Darstellung'
            ),
        ));
        $this->add(array(
            'name' => 'change_password',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Passwort ändern',
                'class' => 'self editor',
            ),
        ));
    }
}