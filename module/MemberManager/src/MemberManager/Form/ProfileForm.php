<?php
namespace MemberManager\Form;

use Zend\Form\Form;

class ProfileForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('profile');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'user_id',
            'type' => 'Text',
            'options' => array(
                'label' => 'Mitgliedsnummer',
            ),
        ));
        $this->add(array(
            'name' => 'state',
            'type' => 'Text',
            'options' => array(
                'label' => 'Mitgliedsstatus',
            ),
        ));
        $this->add(array(
            'name' => 'first_name',
            'type' => 'Text',
            'options' => array(
                'label' => 'Vorname',
            ),
        ));
        $this->add(array(
            'name' => 'last_name',
            'type' => 'Text',
            'options' => array(
                'label' => 'Nachname',
            ),
        ));
        $this->add(array(
            'name' => 'street',
            'type' => 'Text',
            'options' => array(
                'label' => 'Straße und Hausnummer',
            ),
        ));
        $this->add(array(
            'name' => 'zip',
            'type' => 'Text',
            'options' => array(
                'label' => 'Postleitzahl',
            ),
        ));
        $this->add(array(
            'name' => 'city',
            'type' => 'Text',
            'options' => array(
                'label' => 'Stadt',
            ),
        ));
        $this->add(array(
            'name' => 'email',
            'type' => 'Text',
            'options' => array(
                'label' => 'eMail Adresse',
            ),
        ));
        $this->add(array(
            'name' => 'birthday',
            'type' => 'Text',
            'options' => array(
                'label' => 'Geburtsdatum',
            ),
        ));
        $this->add(array(
            'name' => 'phone',
            'type' => 'Text',
            'options' => array(
                'label' => 'Telefonnummer',
            ),
        ));
    }
}
