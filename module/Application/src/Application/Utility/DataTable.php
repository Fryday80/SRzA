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
    public $configuration;

    function __construct() {
        $this->columns = array();
        $this->setDefaultSettings();
    }
    public function add($columnConf) {
        //@todo validate $columnConf
        array_push($this->columns, $columnConf);
    }
    public function setData($data) {
        //@todo validate $data
        $this->data = $data;
    }

    /**
     * set all setting at once
     * @param array $settings
     */
    public function setConf ($index, $value){
        $this->configuration[$index] = $value;
    }

    public function setWholeConf ($settings){
        $this->configuration = array_replace_recursive($this->configuration, $settings);
    }

    private function setDefaultSettings(){
        $this->configuration = array (
            'lengthMenu' => array(array(25, 10, 50, -1), array(25, 10, 50, "All")),
            'select' => "{ style: 'multi' }",
            'buttons' => '',
            'dom' => 'l f r t i p',
        );
        //predefined settings here

        //https://datatables.net/reference/index for preferences/documentation
    }

    /**
     * generates the string to be inserted in the js script <br>
     * uses json_encode
     *
     * @return string js options string
     */
    public function getSetupString(){
        $string = json_encode($this->configuration);
        $string = str_replace('"{', '{', $string);  //todo better way??
        $string = str_replace('}"', '}', $string);
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
            $this->configuration['buttons'] = $setting;
            $this->configuration['dom'] = 'B ' . $this->configuration['dom'];
        }
    }
    public function setLengthMenu() {}  //todo
    public function setSelect() {}      //todo

}