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

        return '<div style="text-align: right;">' . $this->openTag($form) . $formContent . $this->closeTag() . '</div>';
    }

    public function render_center($form)
    {
        if (method_exists($form, 'prepare')) {
            $form->prepare();
        }
        $new_elements = array();
        $formContent = '<br>';

        foreach ($form as $element)
        {
            $label = '';
            if ($element->getLabel() !== NULL) {
                $label = $element->getLabel();
            }
            $input = $this->view->formElement($element);
            $new_element = $label . '<br>' . $input . '<br><br>';
            array_push($new_elements, $new_element);
        }

        return '<div style="text-align: center;">' . $this->openTag($form) . $formContent . $this->closeTag() . '</div>';
    }


    /**
     * Generate an opening form tag
     *
     * @param  null|FormInterface $form
     * @return string
     */
    /*
    public function openTag(FormInterface $form = null, $attributes = array())
    {
        $doctype    = $this->getDoctype();
        $attributes = array();

        if (! (Doctype::HTML5 === $doctype || Doctype::XHTML5 === $doctype)) {
            $attributes = array(
                'action' => '',
                'method' => 'get',
            );
        }

        if ($form instanceof FormInterface) {
            $formAttributes = $form->getAttributes();
            if (!array_key_exists('id', $formAttributes) && array_key_exists('name', $formAttributes)) {
                $formAttributes['id'] = $formAttributes['name'];
            }
            $attributes = array_merge($attributes, $formAttributes);
        }

        if ($attributes) {
            return sprintf('<form %s>', $this->createAttributesString($attributes));
        }

        return '<form>';
    }
    */
}