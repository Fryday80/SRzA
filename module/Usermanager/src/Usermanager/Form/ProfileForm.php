<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 16.11.2016
 * Time: 12:47
 */


namespace Usermanager\Form;

Class ProfileForm extends Form
{
    public function __construct()
    {
        $this->setAttribute('method', 'post');
        // User Table
        $this->add(array(
            'name' => 'username',
            'type' => 'text',
            'attributes' => array(
                'label' => 'Benutzername'
            )
        ));
        $this->add(array(
            'name' => 'email',
            'type' => 'email',
            'attributes' => array(
                'label' => 'eMail'
            )
        ));

        //Profile Table
        $this->add(array(
            'name' => 'prename',
            'type' => 'text',
            'attributes' => array(
                'label' => 'Vorname'
            )
        ));
        $this->add(array(
            'name' => 'name',
            'type' => 'text',
            'attributes' => array(
                'label' => 'Name'
            )
        ));
        $this->add(array(
            'name' => 'street',
            'type' => 'text',
            'attributes' => array(
                'label' => 'Vorname'
            )
        ));
        $this->add(array(
            'name' => 'zip',
            'type' => 'text',
            'attributes' => array(
                'label' => 'PLZ'
            )
        ));
        $this->add(array(
            'name' => 'city',
            'type' => 'text',
            'attributes' => array(
                'label' => 'Ort'
            )
        ));

        //intern Table
        $this->add(array(
            'name' => 'rank',
            'type' => 'text',
            'attributes' => array(
                'label' => 'Mitgliedsstatus'
            )
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'darstellung ',
                'id' => 'submitbutton',
            ),
        ));

    }
}