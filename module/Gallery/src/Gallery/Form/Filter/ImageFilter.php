<?php
namespace Gallery\Form\Filter;

use Zend\InputFilter\InputFilter;

class ImageFilter extends InputFilter
{

    public function __construct()
    {


     // //  $this->add(array(
     //       'name' => 'id',
     //       'required' => true,
     //       'filters' => array(
     //           array(
     //               'name' => 'Int'
     //           )
     //       )
     //   ));
//
     //   $this->add(array(
     //       'name' => 'email',
     //       'required' => true,
     //       'filters' => array(
     //           array(
     //               'name' => 'StripTags'
     //           ),
     //           array(
     //               'name' => 'StringTrim'
     //           )
     //       ),
     //       'validators' => array(
     //           array(
     //               'name' => 'EmailAddress',
     //               'options' => array()
     //           )
     //       )
     //   ));
     //   $this->add(array(
     //       'name' => 'name',
     //       'required' => true,
     //       'filters' => array(
     //           array(
     //               'name' => 'StripTags'
     //           ),
     //           array(
     //               'name' => 'StringTrim'
     //           )
     //       ),
     //       'validators' => array(
     //           array(
     //               'name' => 'StringLength',
     //               'options' => array(
     //                   'encoding' => 'UTF-8',
     //                   'min' => 1,
     //                   'max' => 100
     //               )
     //           )
     //       )
     //   ));
     //   $this->add(array(
     //       'name' => 'password',
     //       'required' => false,
     //       'filters' => array(
     //           array('name' => 'StringTrim')
     //       ),
     //       'validators' => array(
     //           array(
     //               'name' => 'StringLength',
     //               'options' => array(
     //                   'encoding' => 'UTF-8',
     //                   'min' => 4,
     //                   'max' => 32
     //               )
     //           )
     //       )
     //   ));
     //   $this->add(array(
     //       'name' => 'passwordConfirm',
     //       'required' => false,
     //       'validators' => array(
     //           array(
     //               'name'    => 'Identical',
     //               'options' => array(
     //                   'token' => 'password',
     //               ),
     //           ),
     //       ),
     //   ));
    }
}
