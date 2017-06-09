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
    public $data = null;

    public $columns = null;
    
    public $jsConfig = array();

    protected $prepared = false;

    function __construct($config = null)
    {
        $this->setJSDefault();
        if ($config !== null) {
            $this->setupConfig($config);
        }
    }

/*****************PUBLIC methods**************/
    public function setData($data) {
        $this->validateDataType($data, 'setData');
        $this->data = $data;
    }

    /**
     * @param array $link array ( array ('url', 'text') )
     * @param string $columnName
     */
    public function addRowLink(Array $link, $columnName = 'Aktion')
    {
        $insert = '';
        if (isset($link[0])) {
            $i = 0;
            while (isset($link[$i])) {
                $insert .= '<a href="' . $link[$i]['url'] . '">' . $link[$i]['text'] . '</a>';
                $i++;
            }
        }
        elseif (isset($link['url'])){
            $insert .= '<a href="' . $link['url'] . '">' . $link['text'] . '</a>';
        }
        else return;

        foreach ($this->data as $key => $row){
            $this->data[$key][$columnName] = $insert;
        }
        return;
    }
    public function setColumns($columns) {
        //@todo validate $data ??   ja
        foreach ($columns as $key => $value) {
            $this->add($value);
        }
    }
    public function add($columnConf) {
        $columnConf = $this->prepareColumnConfig($columnConf);
        if (! is_array($this->columns) ) $this->columns = array();
        array_push($this->columns, $columnConf);
    }
    public function setJSConfig($jsConfig) {
        //@todo validate $data
        $this->jsConfig = array_replace_recursive($this->jsConfig, $jsConfig);
    }
    public function addJSConfig ($index, $value){
        $this->jsConfig = array_replace_recursive( $this->jsConfig, array($index => $value) );
    }
    /**
     * inserts self made buttons to js DataTableHelper
     * @param string $url
     * @param string $text
     */
    public function insertLinkButton($url, $text){
        // checks if 'buttons' is already set in any way .. if not initializes 'buttons'
        if ( !array_key_exists('buttons', $this->jsConfig) || !is_array($this->jsConfig['buttons']))
        {
            $this->jsConfig['buttons'] = array();
        }
        array_push($this->jsConfig['buttons'], array(
            'action'    => '@buttonFunc:' . $url . '@',
            'text'      => $text,
            'url'       => $url,  //not needed, just for uniformity in the array
        ));
    }
    /**
     * creates the settings for the buttons
     * <br> possible keyword 'all' <br>
     * for array help see: <br>
     * <code>//https://datatables.net/reference/index for preferences/documentation</code>
     * @param array $setting | keyword
     */
    public function setButtons ($setting) {
        if (is_array($setting)) {
            $this->validateDataType($setting, 'setButtons');

            $this->jsConfig = array_replace_recursive($this->jsConfig, array('buttons' => $setting));
            $this->jsConfig['dom']['B'] = true;
        }else{
            if (strtolower($setting) == 'all') {               //@enhancement if needed add array of possible keywords and if them through @todo
                $this->jsConfig = array_replace_recursive($this->jsConfig, array('buttons' => array("print", "copy", "csv", "excel", "pdf")));
                $this->jsConfig['dom']['B'] = true;
            } else {
                $this->validateDataType($setting, 'setButtons');
            }
        }
    }
    /**
     * generates the string to be inserted in the js script <br>
     * uses json_encode
     *
     * @return string js options string
     */
    public function getSetupString()
    {
        $this->prepare();

        $string = json_encode($this->jsConfig);$regex = '/"\@(.+?):(.+?)\@"/';
        preg_match_all($regex, $string, $matches);
        foreach ($matches[1] as $count => $selector ) {
            switch($selector) {
                case 'buttonFunc':
                    //replace with button text
                    $replacement = 'function(){window.location = "' . $matches[2][$count].'";}';
                    $string = str_replace($matches[0][$count], $replacement, $string);
                    break;
            }
        }
        return $string;
    }

    public function prepare()
    {
        if ($this->prepared)return;
        $this->jsPrepare();
        $this->columnPrepare();
        $this->domPrepare();
        $this->prepared = true;
        return;
    }

/*****************PRIVATE methods******************/
    /**
     * validates data, sets up given parts
     * @param array $config
     */
    private function setupConfig($config)
    {
        $config = $this->undoHumanFactor($config);

        if (key_exists('data', $config)){
            $this->setData($config['data']);
        }
        if (key_exists('columns', $config)){
            $this->setColumns($config['columns']);
        }
        if (key_exists('jsConfig', $config)){
            $this->setJSConfig($config['jsConfig']);
        }
    }

    /**
     * sets the default @ _construct
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
     * turns first level array keys into small letters
     * @return array returns refactored array or if no array unchanged data
     * */
    private function undoHumanFactor($array){
        $returnarray = [];
        if ( is_array($array) )
        {
            foreach ($array as $key => $value){
                if (strtolower($key) == 'jsconfig'){
                    $returnarray['jsConfig'] = $value;
                }else {
                    $returnarray[strtolower($key)] = $value;
                }
            }
            return $returnarray;
        } else {
            //@todo error warning?
            return $array;
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
     * inserts self made buttons to js DataTableHelper
     * @param string $url
     * @param string $text
     * @param int    $key numeric key in buttons array
     */
    private function insertLinkButton_internal($url, $text, $key){
        $this->jsConfig['buttons'][$key]['action'] = '@buttonFunc:' . $url . '@';
        $this->jsConfig['buttons'][$key]['text'] = $text;
    }
/*******PREPARE readout*************/
    /**
     * prepares $this->columns for read out
     * <br> if not set => each data set = one column
     */
    private function columnPrepare ()
    {
        if($this->data !== null) {
            //  checks if the column configuration is set
            //  --  //  if yes
            if (count($this->columns) > 0) {
            } //  --  //  if not, each data column is made to visible column
            else {
                foreach ($this->data as $row) {
                    foreach ($row as $key => $value) {
                        $this->add(array(
                            'name' => $key
                        ));
                    }
                    break;
                }
            }
        }
    }
    /**
     * prepares the array of jsConfig for json encode
     */
    private function domPrepare(){
        //fixing DOM settings
        $this->fixDOMArray();

        //rewriting in needed string otherwise there are bugs in the view
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
    private function jsPrepare(){
        if (key_exists('buttons', $this->jsConfig)) {
            foreach ($this->jsConfig['buttons'] as $key => $value) {
                //self made buttons:
                if (is_array($value)) {
                    $this->insertLinkButton_internal($value['url'], $value['text'], $key);
                }
            }
        }
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
            case 'setData':
                $validatorKey = false;
                if ( !is_array($dataToCheck) ) {
                    trigger_error('DataTable -> ' . $requestingFunction . '() > data has to be array', E_USER_ERROR);
                }
                break;
            case 'prepareColumnConfig':
                $validatorKey = 'name';
                break;
            case 'setButtons':
                if (!is_array($dataToCheck)) {
                    trigger_error('DataTable -> ' . $requestingFunction . '() > input is no allowed keyword', E_USER_ERROR);
                } else {
                    $validatorKey = 'text';
                }
                break;
        }

        if ($validatorKey !== false) {
            if (!key_exists($validatorKey, $dataToCheck)) {
                trigger_error('DataTable -> ' . $requestingFunction . '() > key "' . $validatorKey . '" does not exist', E_USER_ERROR);
            }
        }
    }
    /**
     * fixes the dom setup for buttons, if no array given $this->jsConfig
     * <br> if buttons given: checks and fixes the DOM
     * <br> updates the $this->jsConfig
     * @param array $atc | $this->jsConfig if no argument was given
     * @return bool false if no buttons set <br>or<br> true after fixing the settings for buttons
     */
    private function fixDOMArray()
    {
        //  are buttons in the config?
        if (key_exists('buttons', $this->jsConfig) ) {
            //fix forgotten dom setting //can't happen so far
            if (!key_exists('dom', $this->jsConfig)) {
                $this->jsConfig['dom']['B'] = true;
            }
            //  if dom is set
            else {
                //  checks & fixes misspelling -> replaces in case that a 'b' is given in stead of 'B'
                if (key_exists('b', $this->jsConfig['dom'])) {
                    $this->jsConfig['dom']['B'] = $this->jsConfig['dom']['b'];
                    unset ($this->jsConfig['dom']['b']);
                }
                //  adds the "B" if buttons are set up but no dm entry
                if (!key_exists('B', $this->jsConfig['dom'])){
                    $this->jsConfig['dom']['B'] = true;
                }
            }
            return true;
        } else return false;
    }

// @todo @enhancement switching single columns

//   public function columnOff ($array){     //e.g. ->columnOff(array('name' => 'id'))
//       if ( isset ($array['text']) ){
//           unset ( $array['text'] );
//           echo'DataTable -> columnOff: key "text" not allowed as selector';
//       }
//       foreach ($this->columns as $number => $info){
//           foreach ( $array as $key => $selected ) {
//               if ( $this->columns[$number][$key] == $selected ){
//                   unset ( $this->columns[$number] );
//               }
//           }
//       }
//   }

//   public function columnOn ($array){     //e.g. ->columnOff(array('name' => 'id'))
//       if ( !isset ($array['name']) ){
//           trigger_error ( 'DataTable -> columnOff: key "text" not allowed as selector', E_USER_ERROR);
//       }
//       foreach ($this->columns as $number => $info){
//           foreach ( $array as $key => $selected ) {
//               if ( $this->columns[$number][$key] == $selected ){
//                   unset ( $this->columns[$number] );
//               }
//           }
//       }
//   }









}