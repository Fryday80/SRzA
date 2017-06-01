<?php
namespace Application\Form;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

class TestFormFilter extends InputFilter
{
    public function __construct()
    {
/// Text
        $this->add(array(
            'name' => 'Text',//sry ich hab die namen alle kopiert
            'required' => true,
            'allow_empty' => false,
            'filters' => array(
                ['name' => 'StringTrim'],
            ),
            'validators' => array(

                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 3,
                    ),
                ),
            ),
        ));
/// Textarea
        $this->add(array(
            'name' => 'Textarea',
            'required' => true,
            'allow_empty' => false,
        ));
/// Number
        $this->add(array(
            'name' => 'Number',
//            'required' => true,
//            'allow_empty' => false,
        ));
/// Password
        $this->add(array(
            'name' => 'Password',
            'required' => true,
            'allow_empty' => false,
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
            'required' => true,
            'allow_empty' => false,
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 10,
                        'max' => 32
                    )
                )
            )
        ));
/// File
        $this->add(array(
            'name' => 'File',
            'required' => true,
            'allow_empty' => false,
        ));
/// Button
        $this->add(array(
            'name' => 'Button',
            'required' => true,
            'allow_empty' => false,
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
            'required' => true,
            'allow_empty' => false,
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
            'required' => true,
            'allow_empty' => false,
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
            'required' => true,
            'allow_empty' => false,
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
            'required' => true,
            'allow_empty' => false,
        ));
/// Time
        $this->add(array(
            'name' => 'Time',
            'required' => true,
            'allow_empty' => false,
        ));
/// Button
        $this->add(array(
            'name' => 'Button',
            'required' => true,
            'allow_empty' => false,
        ));
/// Radio
        $this->add(array(
            'name' => 'radio',
            'required' => true,
            'allow_empty' => false,

        ));
/// Checkbox
        $this->add(array(
            'name' => 'checkbox',
            'required' => true,
        ));
/// MultiCheckbox
        $this->add(array(
            'name' => 'multiCheckbox',
            'required' => true,
        ));
/// Select
        $this->add(array(
            'name' => 'supervisor_id',
        ));
/// Color
        $this->add(array(
        ));
/// Submit
        $this->add(array(
            'name' => 'submit',
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