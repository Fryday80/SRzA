<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 27.11.2016
 * Time: 00:26
 */

namespace JSCoder\Utility;

use JSCoder\Config\JSRegistration;
use Zend\View\Helper\AbstractHelper;

/**JSCoder <br>
 * View Helper
 * jsCode() returns css and js code for usage in html
 *
 * @package Application\Utility
 */
class JSCoder // needed?     extends AbstractHelper
{
    private $jsModules = array();
    /** JSModule
     * @package Application\/JSCoder
     */
    private $jsData;
    private $registration;

    public function __construct(JSRegistration $registration)
    {
        /**JSRegistration
         * @package Application\JSCoder
         */
        $this->registration =  $registration;
        $this->jsModules = $this->registration->lib; // ->get() returns the same
    }

    /**
     * called method for outputting the html Code
     *
     * @param string $jsModule name of the js module
     * @param string $options optional override of the options, only one option possible //todo perhaps extend for multiple options
     */
    public function render( $jsModule, $options = '' )  //lieber "script"? render ist ja nicht 100% richtig, da die daten ja nicht dabei sind... oder?
    {
        $this->prepare( $jsModule );
        ($options !== '')?: $this->changeOptions( $options );

        $this->renderData();
    }

    /**
     * change a js module setting standard
     *
     * @param string $jsModule  name of the js module as given in JSRegistration
     * @param mixed $settings   the settings
     * @param string $setting   keyword of the setting, default = "options"
     */
    public function setNewStandardSetting ( $jsModule, $settings, $setting = 'options' )
    {
        $this->registration->setNewStandardSettings ( $jsModule, $settings, $setting );
    }
    
    private function renderData()
    {
        $script = $this->completeScript();
        //todo create the htmlcode

        echo /* todo right way */ $this->jsData->jsFile;
        echo '<style>' . /* data of */ $this->jsData->css . '</style>';
        echo '<style>' . /* data of */ $this->jsData->override . '</style>';

        echo '<script>' . $script . '</script>';
    }

    private function completeScript()
    {
        //todo implement json encoding
        //todo implement "inside script" in "script"
        return $readyScript = $this->jsData->script;
    }

    /**
     * change given options / "insideCode" temporarily
     * @param mixed $options options
     * @param string $jsModule selection of the js module
     */
    public function changeOptions( $options, $jsModule = 'none' )
    {
        ($jsModule == 'none')?: $this->prepare( $jsModule );
        // todo string or array depending of missing json implementation
        //meanwhile string is used
        $this->jsData->options = $options;
    }

    /**
     * prepare data of given module
     *
     * @param string $jsModule name of the js module
     */
    private function prepare( $jsModule )
    {
        ( $this->validation( $jsModule ) )?
            $this->jsData = $this->jsModules[$jsModule]
            : $this->errorMessage();
    }

    /**
     * validation of the module
     * @param string $jsModule
     * @return bool
     */
    private function validation( $jsModule )
    {
        return ( array_key_exists($jsModule, $this->jsModules) );
    }
    private function errorMessage()
    {
        dumpd ('this widget is not registered in or managed by JSCoder', 'js usage error', 0); //todo replace dumpd to error trigger
    }
}