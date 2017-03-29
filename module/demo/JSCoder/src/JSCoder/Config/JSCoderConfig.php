<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 26.03.2017
 * Time: 13:22
 */

namespace JSCoder\Config;

require_once(__dir__.'/../Interfaces/IJSCoderConfigFactory.php');
use JSCoder\Interfaces\IJSCoderConfigFactory;

/**
 * Class JSCoderConfig
 *
 * usage in JSCoder.php == class JSCoder
 *
 * @package Application\JSCoder
 */
Class JSCoderConfig implements IJSCoderConfigFactory
{
    private $lib;
    
    public function __construct( $sm )
    {
        $config = $sm->get('config');
        $this->lib = $config['jsModules'];
        $this->validate();
    }

    /**
     * method to get $this->lib
     * @return array $this->lib array of JSModule objects
     */
    public function get()
    {
        return $this->lib;
    }

    private function validate(){
        $default = array(
            'name' => '',
            'information' => '',
            'global' => false,

            'jsFile' => false,

            'hasCss' => false,
            'css' => false,

            'hasOverride' => false,
            'override' => false,

            'script' => false,

            'hasOptions' => false,
            'options' => false,
        );
        foreach ($this->lib as $key => $value){
            $this->lib[$key] = array_replace_recursive($default, $value);
        }
    }
}