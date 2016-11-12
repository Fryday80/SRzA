<?php

namespace TfForm\Form\View\Helper;

use Zend\Form\View\Helper\FormRow as ZfFormRow;
use Zend\Form\ElementInterface;

class FormRow extends ZfFormRow
{
    /**
     * Utility form helper that renders a label (if it exists), an element and errors
     *
     * @param  ElementInterface $element
     * @throws \Zend\Form\Exception\DomainException
     * @return string
     */
    public function render(ElementInterface $element)
    {
        $elementType = substr(get_class($element), strrpos(get_class($element), '\\') + 1);
        $elementTypeClass = 'form-row-'.strtolower($elementType);

        $elementRowId = lcfirst($element->getAttribute('name')).'-row';

        $html = '<div id="'.$elementRowId.'" class="form-row '.$elementTypeClass.'">';
        $html .= parent::render($element);
        $html .= '</div>';

        return $html;
    }
}
