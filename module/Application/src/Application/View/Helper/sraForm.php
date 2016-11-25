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
    private $config = array (
        'form' => array(
            'class' => 'form'
        ),
        'fields' => array(
            'class' => 'fields',
            'label' => array(
                'class' => 'label'
            ),
            'field' => array(
                'class' => 'input',
            ),
        ),
    );

    /**
     * Render a form from the provided $form,
     *
     * @param  FormInterface $form
     * @param $configuration array array of Configuration
     * @return string
     */
    public function render(FormInterface $form, $configuration = false)
    {
        $this->configure($configuration);
        if (method_exists($form, 'prepare')) 
        {
            $form->prepare();
        }
        
        $formContent = '';

        foreach ($form as $element) {
            if ($element instanceof FieldsetInterface) {
                $formContent.= $this->getView()->formCollection($element);
            } else {
                $type = $element->getAttribute('type');
                if ($element->getLabel()!== Null) {
                    $formContent .= '<div ' . $this->getFieldConfigByType('label') . '">' . $element->getLabel() . '</div><div ' . $this->getFieldConfigByType($type) . '">';
                    $formContent .= $this->view->formElement($element) . '</div>';
                } else {
                    $formContent .= $this->view->formElement($element);         // no label => hidden element
                }
            }
        }

        return '<div class="' . $this->getFormConfig() . '">' . $this->openMyTag() . $formContent . $this->closeTag() . '</div>';
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

    private function configure($config=false){
        if ($config) {
            foreach ($config as $key => $value) {
                if ($this->config[$key]) {
                    $this->config[$key] = $value;
                } else {
                    foreach ($this->config as $part => $val) {
                        if ($part[$key]) {
                            $this->config[$part][$key] = $value;
                        } else {
                            foreach ($part as $element => $v) {
                                if ($element[$key]) {
                                    $this->config[$part][$element][$key] = $value;
                                }
                            }
                        }
                    }
                }
            }
        }
    return;
    }

    private function getFormConfig (){
        if (isset($this->config['form'])) {
            $set = $this->config['form'];
        }

        $setup = '';
        foreach ($set as $key => $value){
            $setup .= $key . ' = "' . $value . '" ';
        }
        return $setup;
    }

    private function getFieldConfigByType ($type){
        $set = array();
        if (strtolower($type) == 'label'){
            if (isset ($this->config['fields']['label'])){
                $set = $this->config['fields']['label'];
            }
            else {
                $set = $this->config['fields'];
                unset ($set['field']);
                unset ($set['label']);
            }
        }
        else {
            if (isset($this->config['fields']['field']['type'][$type])) {
                $set = $this->config['fields']['field']['type'][$type];
            }
            else if (isset($this->config['fields']['field'])) {
                $set =  $this->config['fields']['field'];
                unset ($set['type']);
            }
            else if (isset($this->config['fields'])) {
                $set = $this->config['fields'];
                unset ($set['field']);
                unset ($set['label']);
            }
            else {
                if (isset($this->config['form'])) {
                    $set = $this->config['form'];
                }
            }
        }
        $setup = '';
        foreach ($set as $key => $value){
            $setup .= $key . ' = "' . $value . '" ';
        }
        return $setup;
    }

    public function setConfig($config){
        $this->configure($config);
    }

    /**
     * @param $config array e.g.('class' => 'example')
     */
    public function setFormConfig ($config){
        $this->config['form'] = $config;
    }

    /**
     * @param $config array e.g.('class' => 'example')
     */
    public function setFieldsConfig ($config){
        $this->config['fields'] = $config;
    }

    /**
     * @param $config array e.g.('class' => 'example')
     */
    public function setFieldConfig ($config){
        $this->config['fields']['field'] = $config;
    }

    /**
     * @param $type string same as used in form e.g. 'text', 'select', 'number'
     * @param $config array e.g.('class' => 'example')
     */
    public function setFieldConfigByType ($type, $config){
        $this->config['fields']['field']['type'][$type] = $config;
    }

    /**
     * Generate an opening form tag
     *
     * @return string
     */
    public function openMyTag(){
        return '<form '. $this->getFormConfig() . '>';
    }
}