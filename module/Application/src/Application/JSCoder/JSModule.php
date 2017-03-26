<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 26.03.2017
 * Time: 13:22
 */

namespace Application\JSCoder;

/**
 * Class JSModule
 *
 * JSModule is the Object for an js module/snipped used
 *
 * especially for the ones you only use once in a while
 * or just on some pages
 * 
 * usage in JSRegistration.php == class JSRegistration
 *
 * @package Application\JSCoder
 */
class JSModule {
    public $dataArray; // provides the whole data array
    
    public $name;
    public $hasCss = false;   //bool
    public $css;
    public $hasOverride = false;   //bool
    public $override;
    public $script;
    public $hasOptions = false;   //bool
    public $options;
    
    public function __construct(array $data) 
    {
        $this->dataArray = $data;
        foreach ($data as $key => $value)
        {
            $this->$key = $value;
        }
    }
    public function setNewStandardSettings($settings, $setting = 'options')
    {
        //todo validation of data
        $switch = '';
        $settingKey = 'options';
        switch (strtolower($setting)){
            case 'name':
                $settingKey = 'name';
                breake;
            case 'css':
                $switch = 'hasCss';
                $settingKey = 'css';
                breake;
            case 'override':
                $switch = 'hasOverride';
                $settingKey = 'override';
                breake;
            case 'script':
                $settingKey = 'script';
                breake;
            case 'options':
                $switch = 'hasOptions';
                breake;
        }

        ($switch == '') ?: $this->$switch = true;
        $this->$settingKey = $settings;
    }
}