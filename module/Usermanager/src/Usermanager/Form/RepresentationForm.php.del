<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 16.11.2016
 * Time: 12:47
 */


namespace Usermanager\Form;

Class RepresentationForm extends Form
{
    public function __construct()
    {
        $this->setAttribute('method', 'post');
        // User Table
        $this->add(array(
            'name' => 'user_id',
            'type' => 'Hidden', 
        ));


        $this->add(array(
            'name' => 'prename_role',
            'type' => 'Text',
            'attributes' => array(
                'label' => 'Vorname in Darstellung'
            )
        ));
        $this->add(array(
            'name' => 'name_role',
            'type' => 'Text',
            'attributes' => array(
                'label' => 'Name in Darstellung'
            )
        ));
        $this->add(array(
            'name' => 'family',
            'type' => 'Text',
            'attributes' => array(
                'label' => 'Familie'
            )
        ));
        $this->add(array(
            'name' => 'titel_1',
            'type' => 'Text',
            'attributes' => array(
                'label' => 'Titel'
            )
        ));
        $this->add(array(
            'name' => 'titel_2',
            'type' => 'Text',
            'attributes' => array(
                'label' => 'weitere Titel'
            )
        ));
        $this->add(array(
            'name' => 'vita',
            'type' => 'text',
            'attributes' => array(
                'label' => 'vita'
            )
        ));
        $this->add(array(               //fry hide in "show"
            'name' => 'partner_select',
            'type' => 'Select',
            'attributes' => array (
                'label' => 'In Partnerschaft',
                'class' => 'partner_select',
                'options' => array(
                    0 => 'nein / nicht in Darstellung',
                    1 => 'ja'
                ),
            ),
        ));
        $this->add(array(               //fry hide if if "partner_select" = 0
            'name' => 'partner',
            'type' => 'Text',
            'attributes' => array (
                'label' => 'In Partnerschaft',
                'class' => 'partner',
            ),
        ));
        $this->add(array(
            'name' => 'kids',
            'type' => 'Select',               //fry -> 'type' => 'Number' ??
            'attributes' => array (
                'label' => 'Anzahl Kinder',
                'class' => '',
                'options' => array(
                    0 => '--',
                    1 => '1',
                    2 => '2',
                    3 => '3',
                    4 => '4',
                    5 => '5',
                    6 => '6',
                    7 => '7',
                    8 => '8',
                ),
            ),
        ));
        $this->add(array(               //fry upload dependent select??? from defined folder??
            'name' => 'sign',
            'type' => 'text',
            'attributes' => array(
                'label' => 'Wappen'
            )
        ));
        $this->add(array(
            'name' => 'rank',
            'type' => 'Select',
            'attributes' => array (
                'label' => 'Rang / Stand',
                'class' => '',
                'options' => array(
                    0 => 'Tross',
                    1 => 'Soldat',
                    2 => 'Bürgerlicher',
                    3 => 'Ritterfamilie - Kind',
                    4 => 'Ritterfamilie - Partner',
                    5 => 'Ritter - Familienoberhaupt',
                ),
            ),
        ));
        $this->add(array(
            'name' => 'tross_family',
            'type' => 'Select',
            'attributes' => array(
                'label' => 'zugehörig zu',
                'options' => array(
                    0 => 'Adlerfels',
                    1 => 'Leym',
                    2 => 'Ande?',
                ),
            )
        ));
    }
}