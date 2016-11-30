<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 27.11.2016
 * Time: 00:26
 */

namespace Application\Utility;


use ZendDiagnosticsTest\TestAsset\Check\ReturnThis;

/**
 * Class TablehelperConfig <br>
 * object to set and create the config of js plugin datatablehelper
 * <br>
 * ->getSetupString()
 * returns the string to be inserted into the script
 *
 * @package Application\Utility
 */
class TablehelperConfig
{
    private $accessService;
    private $special_1 = false;
    private $special_2 = false;

    private $special_1_Settings;
    private $special_2_Settings;
    private $data;

    function __construct()
    {
        $this->setDefaultSettings();

    }
    /**
     * generates the string to be inserted in the js script <br>
     * uses json_encode
     *
     * @return string js options string
     */
    public function getSetupString(){
        if ($this->owner){
            $this->data = array_replace_recursive($this->data, $this->ownerSettings);
        }
        if ($this->edit_permission){
            $this->data = array_replace_recursive($this->data, $this->editorSettings);
        }

        $string = json_encode($this->data);
        $string = str_replace('"{', '{', $string);  //todo better way??
        $string = str_replace('}"', '}', $string);
        return $string;
    }

    /**
     * sets options to owner settings
     */
    public function setSpecial($number){
        $ 

        this->special_1 = true;
    }

    /**
     * removes owner settings
     */
    public function unsetSpecial(){
        $this->special_1 = false;
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
            $this->data['buttons'] = array("print", "copy", "csv", "excel", "pdf");
            $this->data['dom'] = 'B ' . $this->data['dom'];
        } else if (is_array($setting)){
            $this->data['buttons'] = $setting;
            $this->data['dom'] = 'B ' . $this->data['dom'];
        }
    }

    /**
     * set all setting at once
     * @param array $settings
     */
    public function setAllSettings ($settings){
        $this->data = array_replace_recursive($this->data, $settings);
    }

    private function setDefaultSettings(){
        $this->data = array (
            'lengthMenu' => array(array(25, 10, 50, -1), array(25, 10, 50, "All")),
            'select' => "{ style: 'multi' }",
            'buttons' => '',
            'dom' => 'l f r t i p',
        );
        $this->ownerSettings = array (
            'buttons' => array ("print", "pdf"),
            'dom' => 'B ' . $this->data['dom'],
        );
        $this->editorSettings = array (
            'buttons' => array("print", "copy", "csv", "excel", "pdf"),
            'dom' => 'B ' . $this->data['dom'],
        );

        //https://datatables.net/reference/index for preferences/documentation
    }

    /**
     * sets the special settings <br>
     * allowed specials are 'owner' and 'editor'
     * @param string $who allowed 'owner' and 'editor'
     * @param array $settings
     */
    public function setSpecialSettings ($who, $settings){
        $special = $who . 'Settings';
        $this->$special = array_replace_recursive($this->$special, $settings);
    }
}