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

        $formContent = '';//und die funktion hier müsst eman halt überschreiben das es 

        foreach ($form as $element) {
            $formContent .= '<br>';//sowas machen
            if ($element instanceof FieldsetInterface) {
                $formContent.= $this->getView()->formCollection($element);
            } else {
                $formContent.= $this->getView()->formRow($element);
            }
        }

        return $this->openTag($form) . $formContent . $this->closeTag();
    }
}