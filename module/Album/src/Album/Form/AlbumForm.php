<?php
namespace Album\Form;

use Zend\Form\Form;
use Album\Form\Filter\AlbumFilter;

class AlbumForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('album');
        $this->setAttribute('action', '/album/edit');
        $this->setInputFilter(new AlbumFilter());
        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'folder',
            'type' => 'Text',
            'options' => array(
                'label' => 'Speicherort',
            ),
        ));
        $this->add(array(
            'name' => 'event',
            'type' => 'Text',
            'options' => array(
                'label' => 'Event',
            ),
        ));
        $this->add(array(
            'name' => 'date',
            'type' => 'Text',
            'options' => array(
                'label' => 'Datum',
            ),
        ));
        $this->add(array(
            'name' => 'preview_pic',
            'type' => 'Text',
            'options' => array(
                'label' => 'Vorschaubild',
            ),
        ));
        $this->add(array(
            'name' => 'visibility',
            'type' => 'Select',
            'attributes' => array (
                'id' => 'usernames',
                'options' => array(
                    1 => 'sichtbar',
                    0 =>'nicht sichtbar'
                ),
            ),
                //s'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Sichtbarkeit',
                )
        ));
        $this->add(array(
                'name' => 'timestamp',
                'type' => 'Hidden'
            )
        );

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Speichern',
                'id' => 'submitbutton',
            ),
        ));
    }
}
