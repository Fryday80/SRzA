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
            if ($element instanceof FieldsetInterface) {
                $formContent.= $this->getView()->formCollection($element);
            } else {
                if ($element->getLabel()!== Null) {
                    $formContent .= '<div class="label">' . $element->getLabel() . '</div><div class="input">';
                    $formContent .= $this->view->formElement($element) . '</div>';
                } else {
                    $formContent .= $this->view->formElement($element);
                }
            }
        }

        return '<div class="form">' . $this->openTag($form) . $formContent . $this->closeTag() . '</div>';
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

    public function render_3in_a_row($form)
    {
        //declarations:
        $new_elements = array();
        $hidden_elements = array();
        $submit_elements = array();
        $headers = '';
        $datas = '';
        $input_fields = '';
        $hidden_fields = '';
        $submit_fields ='';
        $i=1;
        $style = ' class ="row3"';
        $formContent = "<table $style>";

        if (method_exists($form, 'prepare')) {
            $form->prepare();
        }

        foreach ($form as $element) {
            $selector = 'new';
            if ($element->getAttribute('type') == 'hidden')
            {
                $selector = 'hidden';
            }
            if ($element->getAttribute('type') == 'submit')
            {
                $selector = 'submit';
            }
            $var = $selector . '_elements';
            $label = '';

            if ($element->getLabel() !== NULL) {
                $label = $element->getLabel();
            }
            $input = $this->view->formElement($element);
            $new_element = array($label, $input);
            array_push($$var, $new_element);
        }

        foreach ($new_elements as $element) {
            if ($i == 3)
            {
                $headers .= '<th>' . $element[0] . '</th></tr>';
                $datas .= '<td>' . $element[1] . '</td></tr>';
                $i =1;

                $input_fields .= $headers . '</tr>' . $datas . '</tr>';
            }
            else if ($i == 2)
            {
                $headers .= '<th>' . $element[0] . '</th>';
                $datas .= '<td>' . $element[1] . '</td>';
                $i++;
            }
            else
            {
                $headers = '<tr><th>' . $element[0] . '</th>';
                $datas = '<tr><td>' . $element[1] . '</td>';
                $i++;
            }

        }
        foreach ($hidden_elements as $element)
        {
            $hidden_fields .= '<td></td><td>' .$element[1] . '</td><td></td>';;
        }
        foreach ($submit_elements as $element)
        {
            $submit_fields .=  '<tr><td></td><td>' .$element[1] . '</td><td></td></tr>';
        }

        $formContent .= $input_fields . ' <tr> ' . $hidden_fields . '</tr>' . $submit_fields;

        $formContent .= '</table>';

        str_replace('</tr></tr>', '</tr>', $formContent);

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