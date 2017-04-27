<?php
/**
 * User: salt
 * Date: 27.04.2017
 * Time: 02:53
 */

namespace Application\View\Helper;


use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\FormElementErrors as BaseFormElementErrors;

class FormElementErrors extends BaseFormElementErrors {
    /**
     * @var array Default attributes for the open format tag
     */
    protected $attributes = array('class' => 'form-error-messages');


}