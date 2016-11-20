<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 19.11.2016
 * Time: 01:42
 */

namespace Application\View\Helper;

use Zend\Form\View\Helper\Form;
use Zend\Form\FormInterface;
use Zend\View\Helper\Doctype;

class sraForm extends Form
{
    /**
     * Render a form from the provided $form,
     *
     * @param  FormInterface $form
     * @return string
     */
    public function render(FormInterface $form)
    {
        if (method_exists($form, 'prepare')) {
            $form->prepare();
        }

        $formContent = '';

        foreach ($form as $element) {
            $formContent .= '<br>';
            if ($element instanceof FieldsetInterface) {
                $formContent.= $this->getView()->formCollection($element);
            } else {
                $formContent.= $this->getView()->formRow($element);
            }
        }

        return '<div class="form" style="text-align: right;">' . $this->openTag($form) . $formContent . $this->closeTag() . '</div>';
    }

    public function render_center($form)
    {
        $style = 'style="width: 100%; text-align: center;"';
        if (method_exists($form, 'prepare')) {
            $form->prepare();
        }
        $new_elements = array();
        $hidden_elements = array();
        $formContent = '<br>';
        foreach ($form as $element) {
            $selector = ($element->getAttribute('type') !== 'hidden')?'new':'hidden';
            $var = $selector . '_elements';
            $label = '';
            if ($element->getLabel() !== NULL) {
                $label = $element->getLabel();
            }
            $input = $this->view->formElement($element);
            $new_element = $label . '<br>' . $input . '<br><br>';
            array_push($$var, $new_element);
        } 
        foreach ($new_elements as $element) {
            $formContent .= $element;
        }
        foreach ($hidden_elements as $element) {
            $formContent .= $element;
        }

        return "<div $style><form $style >" . $formContent . $this->closeTag() . '</div>';
    }


    /**
     * Generate an opening form tag
     *
     * @param  null|FormInterface $form
     * @return string
     */
    public function openMyTag()
    {
        return '<form >';
    }
}