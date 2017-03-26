<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 26.03.2017
 * Time: 13:22
 */

namespace Application\JSCoder;

class JSModule {
    public $dataArray; // provides the whole data array
    
    public $name;
    public $ownCss;
    public $cssPath;
    public $override;
    public $overridePath;
    public $script;
    public $insideCode;
    public $insideCodeValue;
    
    public function __construct(array $data) 
    {
        $this->dataArray = $data;
        foreach ($data as $key => $value)
        {
            $this->$key = $value;
        }
    }
}