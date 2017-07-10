<?php
namespace Application\View\Helper;

use Application\Form\Element;
use Application\Form\Element\TextSearch;
use Zend\Form\View\Helper\FormElement as BaseFormElement;
use Zend\Form\ElementInterface;

class FormElement extends BaseFormElement
{
    public function render(ElementInterface $element) {
        $renderer = $this->getView();
        if (!method_exists($renderer, 'plugin')) {
            // Bail early if renderer is not pluggable
            return '';
        }

        if ($element instanceof TextSearch) {
            $helper = $renderer->plugin('form_text_search');
            return $helper($element);
        }

        return parent::render($element);
    }
}