<?php
namespace Cast\Form;

use Zend\Form\Form;

class AboForm extends Form
{
    public function __construct()
    {
        parent::__construct("Job");
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'id',  //db.user
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'name',//db.abonnementTypes
            'type' => 'text',
            'options' => array(
                'label' => 'Abo Name'
            )
        ));
        $this->add(array(
            'name' => 'monthCosts',//db.abonnementTypes
            'type' => 'text',
            'options' => array(
                'label' => 'monatliche Gebühr'
            )
        ));
        $this->add(array(
            'name' => 'since', //db.abonnements
            'type' => 'text',
            'options' => array(
                'label' => 'läuft seit'
            )
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Submit',
            )
        ));
    }

    /**
     * adds multi-user fields to form including a sum up field 'monthlyTotal'
     * @param int|string $number number of linked multi-users
     */
    public function setMulti($number){
        $number = (int) $number;
        for ($i=0; $i<$number; $i++){
            $this->add(array(
                'name' => 'multi_name_' . $i,  //db.mulriabo->user
                'type' => 'text',
                'options' => array(
                    'label' => 'verlinktes Mitglied'
                )
            ));
            $this->add(array(
                'name' => 'multi_abo_' . $i, //db.multiabo->abonnementTypes
                'type' => 'text',
                'options' => array(
                    'label' => 'verlinktes Abo'
                )
            ));
        }

        $this->add(array(
            'name' => 'monthlyTotal',
            'type' => 'text',
            'options' => array(
                'label' => 'monatliche Kosten zusammen'
            )
        ));
    }
}