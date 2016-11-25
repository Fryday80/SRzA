<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 25.11.2016
 * Time: 13:06
 */

namespace Application\Form\Service;


class FormConfiguration
{
    public $config = array (
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
     * FormconfigurationHelper constructor.
     * @param array $configuration
     */
    function __construct($configuration=false)
    {
        $this->configure($configuration);
    }

    public function getFormConfig (){
        if (isset($this->config['form'])) {
            $set = $this->config['form'];
        }

        $setup = '';
        foreach ($set as $key => $value){
            $setup .= $key . ' = "' . $value . '" ';
        }
        return $setup;
    }

    public function getFieldConfigByType ($type){          //@todo fry include upper level classes
        $set = array();
        $setup = '';
        if (strtolower($type) == 'label'){
            if (isset ($this->config['fields']['label'])){
                $set = $this->config['fields']['label'];
                if (isset($this->config['fields']['class']) && isset($this->config['fields']['label']['class'])){
                    $set['class'] .= $this->config['fields']['class'] . ' ' . $this->config['fields']['label']['class'];
                }
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
        foreach ($set as $key => $value){
            $setup .= $key . ' = "' . $value . '" ';
        }
        return $setup;
    }

    public function configure($config=false){
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

}