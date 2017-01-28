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

    function __construct($config = null)
    {
        $this->columns = array();
        $this->setJSDefault();
        if ($config !== null) {
            $this->prepareConfig($config);
        }
    }

    /**************PUBLIC Access****************/
    public function add($columnConf) {
        $columnConf = $this->prepareColumnConfig($columnConf);
        array_push($this->columns, $columnConf);
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

        /**************UNUSED PUBLICS SO FAR ***************/

    public function setData($data) {
        //@todo validate $data
        $this->data = $data;
    }

    public function setJSConf ($index, $value){
        $this->jsConfig[$index] = $value;
    }

    /**
     * set all js setting at once
     * @param array $settings
     */
    public function setWholeJSConf ($settings){
        $this->jsConfig = array_replace_recursive($this->configuration, $settings);
    }

    /**
     * creates the settings for the buttons
     * <br> possible keyword 'all' <br>
     * for array help see: <br>
     * <code>//https://datatables.net/reference/index for preferences/documentation</code>
     * @param array $setting | keyword
     */
    public function setButtons ($setting) {
        if (strtolower($setting) == 'all'){               //@enhancement if needed add array of possible keywords and if them through @todo
            $this->jsConfig = array_replace_recursive( $this->jsConfig, array( 'buttons' => array( "print", "copy", "csv", "excel", "pdf") ) );
            $this->jsConfig['dom']['B'] = true;
        } else {
            $this->validateDataType($setting, 'setButtons');
            if (is_array($setting)) {
                $this->jsConfig = array_replace_recursive($this->jsConfig, array('buttons' => $setting));
                $this->jsConfig['dom']['B'] = true;
            }
        }
    }

    /**
     * inserts self made buttons to js DataTableHelper
     * @param string $url
     * @param string $text
     */
    public function insertLinkButton($url, $text, $key = false){
        // <external use> checks if 'buttons' is already set in any way .. if not initializes 'buttons'
        if ( !array_key_exists('buttons', $this->jsConfig) || !is_array($this->jsConfig['buttons']))
        {
            $this->jsConfig['buttons'] = array();
        }
        // <internal use> from prepareConfig() a key is given
        if ($key) {
            $this->jsConfig['buttons'][$key]['action'] = '@buttonFunc:' . $url . '@';
        }
        
        // <external use> pushes new button in the buttons array
        else {
            array_push($this->jsConfig['buttons'], array(
                'action'    => '@buttonFunc:' . $url . '@',
                'text'      => $text
            ));
        }
        $this->validateDOMArray();
    }

    public function columnOff ($array){     //e.g. ->columnOff(array('name' => 'id'))
        if ( isset ($array['text']) ){
            unset ( $array['text'] );
            echo'DataTable -> columnOff: key "text" not allowed as selector';
        }
        foreach ($this->columns as $number => $info){
            foreach ( $array as $key => $selected ) {
                if ( $this->columns[$number][$key] == $selected ){
                    unset ( $this->columns[$number] );
                }
            }
        }
    }

    public function columnOn ($array){     //e.g. ->columnOff(array('name' => 'id'))
        if ( !isset ($array['name']) ){
            trigger_error ( 'DataTable -> columnOff: key "text" not allowed as selector', E_USER_ERROR);
        }
        foreach ($this->columns as $number => $info){
            foreach ( $array as $key => $selected ) {
                if ( $this->columns[$number][$key] == $selected ){
                    unset ( $this->columns[$number] );
                }
            }
        }
    }


    /*****************PRIVATE methods******************/
    /**
     * sets the default before customization takes place
     */
    private function setJSDefault(){
        $this->jsConfig = array (
            'lengthMenu' => array(
                array(25, 10, 50, -1),      //values
                array(25, 10, 50, "All")),  //shown values
            'select' => "{ style: 'multi' }",
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
     * @param array $config
     */
    private function prepareConfig($config)
    {
        //  validation for data in $config
        $this->validateDataType($config, 'prepareConfig');

        //  attach the data set
        $this->setData($config['data']);

        //  checks if a column configuration is given
        //  --  //  if yes
        if (key_exists('columns', $config)) {
            foreach ($config['columns'] as $key => $value) {
                $this->add($value);
            }
        }

        //  --  //  if not, each data column is made to visible column
        else {
            foreach ($config['data'] as $row) {
                foreach ($row as $key => $value) {
                    $this->add(array(
                        'name' => $key
                    ));
                }
                break;
            }
        }

        //  checks if a js configuration is given
        if ( key_exists( 'jsConfig', $config ) ){
            //  validate js configuration
            $this->validateDOMArray($config['jsConfig']);
            if (key_exists('buttons', $this->jsConfig)) {
                foreach ($this->jsConfig['buttons'] as $key => $value) {
                    //self made buttons:
                    if (is_array($value)) {
                        $this->insertLinkButton($value['url'], $value['text'], $key);
                    }
                }
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

    /**
     * prepares the array of jsConfig for json encode
     */
    private function domPrepare(){
        $this->validateDOMArray();
        //rewriting in needed string
        $domPrepare = '';
        $sorting_array = array (
            0 => 'B',
            1 => 'l',
            2 => 'f',
            3 => 'r',
            4 => 't',
            5 => 'i',
            6 => 'p'
        );
        foreach ($sorting_array as $position => $option){
            $domPrepare .= (isset( $this->jsConfig['dom'][$option] )) ? $option . ' ' : '';
        }
        $this->jsConfig['dom'] = $domPrepare;
    }

    /***********VALIDATION********************/

    /**
     * validates data types and throws error in case
     * @param $dataToCheck
     * @param $requestingFunction
     */
    private function validateDataType($dataToCheck, $requestingFunction)
    {
        switch ($requestingFunction) {
            case 'prepareConfig':
                $validatorKey = 'data';
                if (key_exists($validatorKey, $dataToCheck) && !is_array($dataToCheck['data'])) {
                    trigger_error('DataTable -> ' . $requestingFunction . '() > key "' . $validatorKey . '" has to be array', E_USER_ERROR);
                }
                break;
            case 'prepareColumnConfig':
                $validatorKey = 'name';
                break;
            case 'setButtons':
                if (!is_array($dataToCheck)) {
                    trigger_error('DataTable -> ' . $requestingFunction . '() > "' . $dataToCheck . '" is neither a allowed keyword nor valid array', E_USER_ERROR);
                } else {
                    $validatorKey = 'text';
                }
                break;
        }

        if (!key_exists($validatorKey, $dataToCheck)) {
            trigger_error('DataTable -> ' . $requestingFunction . '() > key "' . $validatorKey . '" does not exist', E_USER_ERROR);
        }
    }

    /**
     * validates the dom setup for buttons, if no array given $this->jsConfig
     * <br> if buttons given: checks and fixes the DOM
     * <br> updates the $this->jsConfig
     * @param array $atc | $this->jsConfig if no argument was given
     * @return bool false if no buttons set <br>or<br> true after fixing the settings for buttons
     */
    private function validateDOMArray($atc = Null)
    {
        //  sets $this->jsConfig if no argument was given
        $arrayToCheck = ($atc == Null) ? $this->jsConfig : $atc;
        //  are buttons in the config?
        if (key_exists('buttons', $arrayToCheck) ) {
            //fix forgotten dom setting
            if (!key_exists('dom', $arrayToCheck)) {
                $arrayToCheck['dom']['B'] = true;
            }
            //  if dom is set
            else {
                //  checks & fixes misspelling -> replaces in case that a 'b' is given in stead of 'B'
                if (key_exists('b', $arrayToCheck['dom'])) {
                    $arrayToCheck['dom']['B'] = $arrayToCheck['dom']['b'];
                    unset ($arrayToCheck['dom']['b']);
                }
                //  adds the "B" if buttons are set up but no dm entry
                if (!key_exists('B', $arrayToCheck['dom'])){
                    $arrayToCheck['dom']['B'] = true;
                }
            }
            $this->jsConfig = array_replace_recursive($this->jsConfig, $arrayToCheck);
            return true;
        } else return false;
    }


}