<?php
namespace Application\Form;

use Zend\Form\Form;

class TestForm extends Form
{
    public function __construct()
    {
        parent::__construct("Test");
        $this->setAttribute('method', 'post');
        $this->setInputFilter(new TestFormFilter());

/// TextSearch
        $this->add(array(
            'name' => 'TextSearch',
            'type' => Element\TextSearch::class,
            'required' => true,
            'attributes' => array(
                'placeholder' => 'Text',
            ),
            'options' => array(
                'label' => 'TextSearch',
                'value_options' => array('eins' => 'Eins', 'zwei' => 'Zwei'),
            ),
        ));
/// Text
        $this->add(array(
            'name' => 'Text',
            'type' => 'text',
            'attributes' => array(
                'placeholder' => 'Text',
            ),
            'options' => array(
                'label' => 'Text'
            ),
        ));
/// Textarea
        $this->add(array(
            'name' => 'Textarea',
            'type' => 'Zend\Form\Element\Textarea',
            'attributes' => array(
                'placeholder' => 'Textarea'
            ),
            'options' => array(
                'label' => 'Textarea',
            ),
        ));
/// Number
        $this->add(array(
            'name' => 'Number',
            'type' => 'Zend\Form\Element\Number',
            'attributes' => array(
                'placeholder' => 'Number'
            ),
            'options' => array(
                'label' => 'Number',
            ),
        ));
/// Password
        $this->add(array(
            'name' => 'Password',
            'type' => 'Zend\Form\Element\Password',
            'attributes' => array(
                'placeholder' => 'Password'
            ),
            'options' => array(
                'label' => 'Password',
            ),
        ));
/// Range
//        $this->add(array(
//            'name' => 'Range',
//            'type' => 'Zend\Form\Element\Range',
//            'options' => array(
//                'label' => 'Range',
//            ),
//            'required' => true,
//            'allow_empty' => false,
//        ));
/// Url
        $this->add(array(
            'name' => 'Url',
            'type' => 'Zend\Form\Element\Url',
            'attributes' => array(
                'placeholder' => 'Url'
            ),
            'options' => array(
                'label' => 'Url',
            ),
        ));
/// File
        $this->add(array(
            'name' => 'File',
            'type' => 'Zend\Form\Element\File',
            'options' => array(
                'label' => 'File',
            ),
        ));
/// File
		$this->add(array(
			'name' => 'File2',
			'type' => 'Zend\Form\Element\File',
			'options' => array(
				'label' => 'File2',
			),
		));
/// Button
        $this->add(array(
            'name' => 'Button',
            'type' => 'Zend\Form\Element\Button',
            'options' => array(
                'label' => 'Button',
            ),
        ));

/// CAPTCHA   https://samsonasik.wordpress.com/2012/09/12/zend-framework-2-using-captcha-image-in-zend-form/
//        $dirdata = './data';
//
//        //pass captcha image options
//        $captchaImage = new Capture CaptchaImage(  array(
//                'font' => $dirdata . '/fonts/arial.ttf',
//                'width' => 250,
//                'height' => 100,
//                'dotNoiseLevel' => 40,
//                'lineNoiseLevel' => 3)
//        );
//        $captchaImage->setImgDir($dirdata.'/captcha');
//        $captchaImage->setImgUrl(null);
//
//        //add captcha element...
//        $this->add(array(
//            'type' => 'Zend\Form\Element\Captcha',
//            'name' => 'captcha',
//            'options' => array(
//                'label' => 'Please verify you are human',
//                'captcha' => $captchaImage,
//            ),
//        ));



/// Data
        $this->add(array(
            'name' => 'date',
            'type' => 'Zend\Form\Element\Date',
            'options' => array(
                'label' => 'Data',
            ),
        ));
/// DataSelect
//        $this->add(array(
//            'name' => 'DataSelect',
//            'type' => 'Zend\Form\Element\DataSelect',
//            'options' => array(
//                'label' => 'DataSelect',
//                'create_empty_option' => true,
//                'min_year'            => date('Y') - 0,
//                'max_year'            => date('Y') + 6,
//                'day_attributes' => array(
//                    'data-placeholder' => 'Tag',
//                    'style' => 'width: 20%'
//                ),
//                'month_attributes' => array(
//                    'data-placeholder' => 'Monat',
//                    'style' => 'width: 20%'
//                ),
//                'year_attributes' => array(
//                    'data-placeholder' => 'Jahr',
//                    'style' => 'width: 20%'
//                )
//            ),
//            'required' => true,
//            'allow_empty' => false,
//        ));
/// DataTime
//        $this->add(array(
//            'name' => 'DataTime',
//            'type' => 'Zend\Form\Element\DataTime',
//            'options' => array(
//                'label' => 'DataSelect',
//            ),
//            'required' => true,
//            'allow_empty' => false,
//        ));
/// DateTimeLocal
        $this->add(array(
            'name' => 'DateTimeLocal',
            'type' => 'Zend\Form\Element\DateTimeLocal',
            'options' => array(
                'label' => 'DateTimeLocal',
            ),
        ));
/// DateTimeSelect       requires the intl PHP extension
//        $this->add(array(
//            'name' => 'DateTimeSelect',
//            'type' => 'Zend\Form\Element\DateTimeSelect',
//            'options' => array(
//                'label' => 'DateTimeSelect',
//            ),
//            'required' => true,
//            'allow_empty' => false,
//        ));
/// Month
        $this->add(array(
            'name' => 'Month',
            'type' => 'Zend\Form\Element\Month',
            'options' => array(
                'label' => 'Month',
            ),
        ));
/// MonthSelect     requires the intl PHP extension
//        $this->add(array(
//            'name' => 'MonthSelect',
//            'type' => 'Zend\Form\Element\MonthSelect',
//            'options' => array(
//                'label' => 'MonthSelect',
//                'min_year' => 1986,
//            ),
//            'required' => true,
//            'allow_empty' => false,
//        ));
/// Week
        $this->add(array(
            'name' => 'Week',
            'type' => 'Zend\Form\Element\Week',
            'options' => array(
                'label' => 'Week',
            ),
        ));
/// Time
        $this->add(array(
            'name' => 'Time',
            'type' => 'Zend\Form\Element\Time',
            'options' => array(
                'label' => 'Time',
            ),
        ));
/// Button
        $this->add(array(
            'name' => 'Button',
            'type' => 'Zend\Form\Element\Button',
            'options' => array(
                'label' => 'Button',
            ),
        ));
/// Radio
        $this->add(array(
            'name' => 'radio',
            'type' => 'Zend\Form\Element\Radio',
            'attributes' => array(),
            'options' => array(
                'label' => 'Radio',
                'value_options' => array(
                    'm' => 'One',
                    'f' => 'Two',
                ),
            ),

        ));
/// Checkbox
        $this->add(array(
            'name' => 'checkbox',
            'type' => 'Zend\Form\Element\Checkbox',
            'options' => array(
                'label' => 'Checkbox',
            ),
        ));
/// MultiCheckbox
        $this->add(array(
            'name' => 'multiCheckbox',
            'type' => 'Zend\Form\Element\MultiCheckbox',
            'options' => array(
                'label' => 'MultiCheckbox',
                'value_options' => array(
                    '0' => 'Apple',
                    '1' => 'Orange',
                    '2' => 'Lemon'
                ),
            ),
        ));
/// Select
        $this->add(array(
            'name' => 'supervisor_id',
            'type' => 'Zend\Form\Element\Select',
            'required' => true,
            'options' => array(
                'label' => 'Select',
                'value_options' => array(0 => 'Eins', 1 => 'Zwei'),
            )
        ));
/// Color
        $this->add(array(
            'name' => 'Color',
            'type' => 'Zend\Form\Element\Color',
            'required' => true,
            'options' => array(
                'label' => 'Color',
            )
        ));
/// Submit
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'required' => true,
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Submit',
            )
        ));
/// Image   stupid
//        $this->add(array(
//            'name' => 'Image',
//            'type' => 'Zend\Form\Element\Image',
//            'attributes' => array(
//                'src' => 'http://localhost/media/file/gallery/Facebook/13958099_1234116073318845_8635213061334021213_o.jpg'
//            ),
//            'options' => array(
//                'label' => 'Image',
//            ),
//            'required' => true,
//            'allow_empty' => false,
//        ));
    }

}