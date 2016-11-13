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
        $general_layout = array(
            'class' => 'width_40 margin_left_5 margin_right_15'
            // https://framework.zend.com/manual/1.10/en/learning.form.decorators.intro.html
        );
        
        $this->setAttribute('action', '/album/edit');
        $this->setInputFilter(new AlbumFilter());
        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'folder',
            'type' => 'Text',
            'attributes' => $general_layout,
            'options' => array(
                'label' => 'Speicherort',
            ),
        ));
        $this->add(array(
            'name' => 'event',
            'type' => 'Text',
            'attributes' => $general_layout,
            'options' => array(
                'label' => 'Event',
            ),
            'decorators' => array(
                //'ViewHelper',
                //'Label' => array(
                //    'tag' => 'dr'
                //),
                'HtmlTag' => array(
                    'tag' => 'br',
                    'openOnly' => false,
                    'placement' => 'prepend',
                ),
            ),
        ));
        $this->add(array(
            'name' => 'date',
            'type' => 'Text',
            'attributes' => $general_layout,
            'options' => array(
                'label' => 'Datum',
            ),
            'decorators' => array(
                'ViewHelper',
                'Label' => array(
                    'tag' => 'dr'
                ),
                'HtmlTag' => array(
                    'tag' => 'br',
                    'openOnly' => true,
                    'placement' => 'prepend',
                ),
            ),
        ));
        $this->add(array(
            'name' => 'preview_pic',
            'type' => 'Text',
            'attributes' => $general_layout,
            'options' => array(
                'label' => 'Vorschaubild',
            ),
        ));
        $this->add(array(
            'name' => 'visibility',
            'type' => 'Select',
            'attributes' => array (
                'class' => 'width_40 margin_left_5 margin_right_15',
                'options' => array(
                    1 => 'sichtbar',
                    0 =>'nicht sichtbar'
                ),
            ),
            'options' => array(
                'label' => 'Sichtbarkeit des Albums',
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
            'attributes' => $general_layout,
            'attributes' => array(
                'value' => 'Speichern',
                'id' => 'submitbutton',
            ),
        ));
    }
}
