<?php
namespace Application\Model\AbstractModels;

/**
 * Class DataTableAbstract <br/>
 * @link https://datatables.net/reference/index Documentation of DataTable for preferences/documentation
 * @see Application\View\Helper\DataTableHelper
 * @package Application\Utility
 */
abstract class DataTableAbstract
{ 
    public $data = null;
    public $columns = array();
    public $jsConfig = array (
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
                                'p' => true
                            )
    );

    protected $defaultColumn = array(
        'name'  => 'name',
        'label' => 'label',
        'type'  => 'text',
    );

    protected $prepared = false;

    /**
     * DataTable constructor.
     * @param mixed $config null, array, data object, array of data objects, <br/>
     *      array ( <br/>
     *          ['data' => data,] <br/>
     *          ['columns' => columns,] <br/>
     *          ['jsConfig' => jsConfig,] <br/>
     *      )
     */
    public function __construct($config = null)
    {
        if ($config !== null) {
            $this->checkConfig($config);
        }
    }

    /**
     * Set Data
     * @param mixed $data array, array of objects or object with data
     */
    public function setData($data)
    {
        if (is_object($data)){
        	if ($data instanceof IToArray)
				$data = $data->toArray();
            elseif (method_exists($data, 'toArray'))
                $data = $data->toArray();
            else
                $data = get_object_vars($data);
        }
        foreach ($data as &$item) {
            if (is_object($item)){
				if ($data instanceof IToArray)
					$data = $data->toArray();
				elseif (method_exists($item, 'toArray'))
                    $item = $item->toArray();
                else
                    $item = get_object_vars($item);
            }
            else break;
        }
        $this->validateDataType($data, 'setData');
        $this->data = $data;
    }



    /**
     * Set array of columns
     *
     * @param array $columns <br/>
     *              array ( array ( <br/>
     *                  'name'   => string (matching with data column to insert its data) <br/>
     *                  'label'  => string, <br/>
     *                  'type'   => 'text' | 'custom', <br/>
     *                  'render' => function($row) , if type = custom: $row is single data row, returns html string <br/>
     *              ))
     */
    public function setColumns($columns)
    {
        $this->columns = array();
        foreach ($columns as $value) {
            $this->addColumn($value);
        }
    }

    /**
     * add a column
     *
     * @param array $columnConf <br/>
     *              array ( <br/>
     *                  'name'   => string (matching with data column to insert its data) <br/>
     *                  'label'  => string, <br/>
     *                  'type'   => 'text' | 'custom', <br/>
     *                  'render' => function($row) , if type = custom: $row is single data row, returns html string <br/>
     *              )
     */
    public function addColumn(array $columnConf)
    {
        $this->validateDataType($columnConf, 'prepareColumnConfig');
        $new = $columnConf + $this->defaultColumn;
        if ($new['label'] == 'label') $new['label'] = $new['name'];
        array_push($this->columns, $new);
    }

    /**
     * Resets columns to standard <br/>
     * if not defined afterwards all columns will be shown
     */
    public function resetColumns()
    {
        $this->columns = array();
        $this->prepared = false;
    }

    public function removeColumnByName($name)
    {
        $this->removeColumn('name', $name);
    }

    public function removeColumnByLabel($label)
    {
        $this->removeColumn('label', $label);
    }

    protected function setJSConfig($jsConfig)
    {
        $this->jsConfig = array_replace_recursive($this->jsConfig, $jsConfig);
    }

//    public function addJSConfig ($index, $value)
//    {
//        $this->jsConfig = array_replace_recursive( $this->jsConfig, array($index => $value) );
//    }

    /**
     * inserts self made buttons to js DataTableHelper header
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
     * Creates the settings for the buttons
     * <br> possible keyword 'all' <br>
     *
     * @link //https://datatables.net/reference/index for preferences/documentation
     * @param array $setting | keyword
     */
    public function setButtons ($setting)
    {
        if (is_array($setting)) {
            $this->validateDataType($setting, 'setButtons');

            $this->jsConfig = array_replace_recursive($this->jsConfig, array('buttons' => $setting));
            $this->jsConfig['dom']['B'] = true;
        } else {
            if (strtolower($setting) == 'all') {               //@enhancement if needed add array of possible keywords and if them through
                $this->jsConfig = array_replace_recursive($this->jsConfig, array('buttons' => array("print", "copy", "csv", "excel", "pdf")));
                $this->jsConfig['dom']['B'] = true;
            } else {
                $this->validateDataType($setting, 'setButtons');
            }
        }
    }

    /**
     * Generates the string to be inserted in the js script tag<br>
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

    /**
     * @internal
     * inserts self made buttons to js DataTableHelper headline
     * @param string $url
     * @param string $text
     * @param int    $key numeric key in buttons array
     */
    protected function insertLinkButton_internal($url, $text, $key){
        $this->jsConfig['buttons'][$key]['action'] = '@buttonFunc:' . $url . '@';
        $this->jsConfig['buttons'][$key]['text'] = $text;
    }

    /**
     * @internal
     * Remove method to remove columns by $key and $value
     * @param $key
     * @param $value
     */
    protected function removeColumn($key, $value)
    {
        if (empty($this->columns)) $this->columnPrepare();
        foreach ($this->columns as $cKey => $column){
            if ($column[$key] == $value) unset($this->columns[$cKey]);
        }
    }

//============================================================== check on construct
    /**
     * Checks the given data in __construct
     * @param $config
     * @return bool
     */
    protected function checkConfig($config)
    {
        // config needs to be array or object else return false
        if (!is_array ($config) && !is_object($config)) return false;
        // if config is object -> object is data Set
        if (is_object($config))
            $this->setData($config);
        // if config is array check for config keys
        else {
            $isConfigArray = false;
            if (key_exists('data', $config)) {
                $this->setData($config['data']);
                $isConfigArray = true;
            }
            if (key_exists('columns', $config)) {
                $this->setColumns($config['columns']);
                $isConfigArray = true;
            }
            if (key_exists('jsConfig', $config)) {
                $this->setJSConfig($config['jsConfig']);
                $isConfigArray = true;
            }
            // if data is given as array of objects or as array
            if (!$isConfigArray){
                $this->setData($config);
            }
        }
        return true;
    }

//========================================== Validation
    /**
     * validates data types and throws error in case
     * @param $dataToCheck
     * @param $requestingMethod
     */
    protected function validateDataType($dataToCheck, $requestingMethod)
    {
        switch ($requestingMethod) {
            case 'setData':
                $validatorKey = false;
                if ( !is_array($dataToCheck) ) {
                    trigger_error('DataTable -> ' . $requestingMethod . '() > data has to be array', E_USER_ERROR);
                }
                break;
            case 'prepareColumnConfig':
                $validatorKey = 'name';
                break;
            case 'setButtons':
                if (!is_array($dataToCheck)) {
                    trigger_error('DataTable -> ' . $requestingMethod . '() > input is no allowed keyword', E_USER_ERROR);
                } else {
                    $validatorKey = 'text';
                }
                break;
        }

        if ($validatorKey !== false) {
            if (!key_exists($validatorKey, $dataToCheck)) {
                trigger_error('DataTable -> ' . $requestingMethod . '() > key "' . $validatorKey . '" does not exist', E_USER_ERROR);
            }
        }
    }

//========================================================= prepare
    public function prepare()
    {
        if ($this->prepared) return;
    // ==== prepare JS
        if (key_exists('buttons', $this->jsConfig)) {
            foreach ($this->jsConfig['buttons'] as $key => $value) {
                //self made buttons:
                if (is_array($value)) {
                    $this->insertLinkButton_internal($value['url'], $value['text'], $key);
                }
            }
        }
    // ==== prepare columns array
        if($this->data !== null && empty($this->columns)) {
            foreach ($this->data as $row) {
                foreach ($row as $key => $value) {
                    $this->addColumn(array(
                        'name' => $key
                    ));
                }
                break;
            }
        }
        if (empty($this->columns)) {
			$this->addColumn(array(
				'name' => 'noData Given'
			));
		}
    // ==== prepare DOM array
        //fixing DOM settings
        $this->fixDOMArray();
        //rewriting in needed string otherwise there are bugs in the view
        $domPrepare = '';
        $sorting_array = array ( 'B', 'l', 'f', 'r', 't', 'i', 'p' );
        foreach ($sorting_array as $option){
            $domPrepare .= (isset( $this->jsConfig['dom'][$option] )) ? $option . ' ' : '';
        }
        $this->jsConfig['dom'] = $domPrepare;
        $this->prepared = true;
        return;
    }

    /**
     * Fixes the dom setup for buttons, if no array given $this->jsConfig
     * <br> if buttons given: checks and fixes the DOM
     * <br> updates the $this->jsConfig
     * @return bool false if no buttons set <br>or<br> true after fixing the settings for buttons
     */
    protected function fixDOMArray()
    {
        //  are buttons in the config?
        if (key_exists('buttons', $this->jsConfig) ) {
            //fix forgotten dom setting //can't happen so far
            if (!key_exists('dom', $this->jsConfig)) $this->jsConfig['dom']['B'] = true;
            //  if dom is set
            else {
                //  checks & fixes misspelling -> replaces in case that a 'b' is given in stead of 'B'
                if (key_exists('b', $this->jsConfig['dom'])) {
                    $this->jsConfig['dom']['B'] = $this->jsConfig['dom']['b'];
                    unset ($this->jsConfig['dom']['b']);
                }
                //  adds the "B" if buttons are set up but no dom entry
                if (!key_exists('B', $this->jsConfig['dom'])) $this->jsConfig['dom']['B'] = true;
            }
            return true;
        } else return false;
    }
}