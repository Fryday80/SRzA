<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 19.11.2016
 * Time: 01:42
 */

namespace Application\View\Helper;

use Application\Utility\FormConfiguration;
use Zend\Form\View\Helper\Form;
use Zend\Form\FormInterface;
use Zend\View\Helper\Doctype;

class sraForm extends Form
{
    private $config;

    /**
     * Render a form from the provided $form,
     *
     * @param  FormInterface $form
     * @param bool|object $configurationService object instance of configurationService or nothing
     * @return string
     */
   // public function render(FormInterface $form, $configurationService = false)
   // {
   //     if (! $configurationService) {
   //         $configurationService = new FormConfiguration();
   //     }
   //     $this->config = $configurationService;
   //     if (method_exists($form, 'prepare'))
   //     {
   //         $form->prepare();
   //     }
   //
   //     $formContent = '';
//
   //     foreach ($form as $element) {
   //         if ($element instanceof FieldsetInterface) {
   //             $formContent.= $this->getView()->formCollection($element);
   //         } else {
   //             $type = $element->getAttribute('type');
   //             if ($element->getLabel()!== Null) {
   //                 $formContent .= '<div ' . $this->config->getFieldConfigByType('label') . '">' . $element->getLabel() . '</div><div ' . $this->config->getFieldConfigByType($type) . '">';
   //                 $formContent .= $this->view->formElement($element) . '</div>';
   //             } else {
   //                 $formContent .= $this->view->formElement($element);         // no label => hidden element
   //             }
   //         }
   //     }
//
   //     return '<div class="' . $this->config->getFormConfig() . '">' . $this->openMyTag() . $formContent . $this->closeTag() . '</div>';
   // }

    /**
     * Generate an opening form tag
     *
     * @return string
     */
    public function openMyTag(){//aber des file is komisch
        return '<form '. $this->config->getFormConfig() . '>';
    }
}