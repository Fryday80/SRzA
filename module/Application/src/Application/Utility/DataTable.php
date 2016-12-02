<?php
/**
 * Created by PhpStorm.
 * User: salt
 * Date: 28.11.2016
 * Time: 18:39
 */

namespace Application\Utility;


class DataTable
{
    public $data;
    public $columns;
    public $jsConfig;

    function __construct()
    {
        $this->columns = array();
        $this->setJSDefault();

        //build dependency of passed arguments //fry erase in time
        $arguments = func_get_args();
        $i = func_num_args();
        if (method_exists($this,$function='__construct'.$i)) {
            call_user_func_array(array($this,$function),$arguments);
        }
    }

    function __construct1 ($config){
        if ($config !== null) {
            $this->prepareConfig($config);
        }
    }

    public function add($columnConf) {
        $columnConf = $this->prepareColumnConfig($columnConf);
        array_push($this->columns, $columnConf);
    }
    public function setData($data) {
        //@todo validate $data
        $this->data = $data;
    }
    public function setConf ($index, $value){
        $this->configuration[$index] = $value;
    }
    /**
     * set all setting at once
     * @param array $settings
     */
    public function setWholeConf ($settings){
        $this->configuration = array_replace_recursive($this->configuration, $settings);
    }
    private function setJSDefault(){
        $this->jsConfig = array (
            'lengthMenu' => array(
                array(25, 10, 50, -1),      //values
                array(25, 10, 50, "All")),  //shown values
            'select' => "{ style: 'multi' }",
            'buttons' => '',
            'dom' => array(
                'l' => true,
                'f' => true,
                'r' => true,
                't' => true,
                'i' => true,
                'p' => true)
        );
        //https://datatables.net/reference/index for preferences/documentation
    }
    /**
     * generates the string to be inserted in the js script <br>
     * uses json_encode
     *
     * @return string js options string
     */
    public function getSetupString(){
        $this->domPrepare();

        $string = json_encode($this->jsConfig);
        $regex = '/"\@buttonFunc:(.*)\@"/i';
        $func = 'function(){window.location = "$1";}';
        $string = preg_replace($regex, $func, $string);
        return $string;
    }
    /**
     * creates the settings for the buttons <br>
     * possible keyword 'all' <br>
     * for array help see: <br>
     * <code>//https://datatables.net/reference/index for preferences/documentation</code>
     * @param array|keyword $setting
     */
    public function setButtons ($setting) {
        if (strtolower($setting) == 'all'){
            $this->configuration['buttons'] = array("print", "copy", "csv", "excel", "pdf");
            $this->configuration['dom'] = 'B ' . $this->configuration['dom'];
        } else if (is_array($setting)){
            $this->configuration['buttons'] = array_replace_recursive($this->configuration['buttons'], $setting);
            $this->configuration['dom'] = 'B ' . $this->configuration['dom']; //todo might end up in "B B l f..."
        }
    }
    /**
     * @param array $config
     */
    private function prepareConfig($config)
    {
        $this->validateDataType($config, 'prepareConfig');

        $this->setData($config['data']);
        if (key_exists('columns', $config)) {
            foreach ($config['columns'] as $key => $value) {
                $this->add($value);
            }
        } else {
            foreach ($config['data'] as $row) {
                foreach ($row as $key => $value) {
                    $this->add(array(
                        'name' => $key
                    ));
                }
                break;
            }
        }
        if ( isset( $config['jsConfig'] ) ){
            $this->jsConfig = array_replace_recursive ( $this->jsConfig, $config['jsConfig'] );
            foreach ($this->jsConfig['buttons'] as $key => $value) {
                if ( is_array( $value ) )
                    $this->jsConfig['buttons'][$key]['action'] = '@buttonFunc:' . $value['url'] . '@';
            }
        }
    }

    private function prepareColumnConfig($config){
        $this->validateDataType($config, 'prepareColumnConfig');

        $defaultConfColl = array(
            'type' => 'text',
        );
        $columnConf = array_replace_recursive($defaultConfColl, $config);
        if (! key_exists('label', $columnConf)) {
            $columnConf['label'] = $columnConf['name'];
        }
        return $columnConf;
    }

    private function validateDataType($arrayToCheck, $requestingFunction)
    {
        switch ($requestingFunction) {
            case 'prepareConfig':
                $validatorKey = 'data';
                if (key_exists($validatorKey, $arrayToCheck) && !is_array($arrayToCheck['data'])) {
                    trigger_error('DataTable -> ' . $requestingFunction . '() > key "' . $validatorKey . '" has to be array', E_USER_ERROR);
                }
                //fix misspelling and forgottoen dom setting
                if (key_exists ( 'buttons', $arrayToCheck ) ){
                    if ( !key_exists ( 'dom', $arrayToCheck ) ){
                        $arrayToCheck['dom']['B'] = true;
                    } else {
                        if ( key_exists ( 'b', $arrayToCheck['dom'] ) ){
                            $arrayToCheck['dom']['B'] = $arrayToCheck['dom']['b'];
                            unset ($arrayToCheck['dom']['b']);
                        }
                    }
                }
            break;
            case 'prepareColumnConfig':
                $validatorKey = 'name';
            break;
        }

        if (!key_exists($validatorKey, $arrayToCheck)) {
            trigger_error('DataTable -> ' . $requestingFunction . '() > key "' . $validatorKey . '" does not exist', E_USER_ERROR);
        }
        return $arrayToCheck;
    }
    private function domPrepare(){
        $domPrepare = '';
        $sorting_array = array ( 'B', 'l', 'f', 'r', 't', 'i', 'p');
        foreach ($sorting_array as $position => $option){
            $domPrepare .= ($this->jsConfig['dom'][$option] !== false) ? $option . ' ' : '';
        }
        $this->jsConfig['dom'] = $domPrepare;
    }
}