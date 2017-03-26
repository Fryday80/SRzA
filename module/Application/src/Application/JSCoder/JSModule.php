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
    public $ownCss;
    public $cssPath;
    public $override;
    public $overridePath;
    public $script;
    public $optionCode;
    public $optionCodeValue;
    
    public function __construct(array $data) 
    {
        $this->dataArray = $data;
        foreach ($data as $key => $value)
        {
            $this->$key = $value;
        }
    }
    public function setNewStandardOption($option)
    {
        $this->optionCode = true;
        $this->optionCode = $option;
    }
}