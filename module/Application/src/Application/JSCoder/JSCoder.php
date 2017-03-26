<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 27.11.2016
 * Time: 00:26
 */

namespace Application\JSCoder;
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
    private $jsData = array();
    private $registration;

    public function __construct()
    {
        /**JSRegistration
         * @package Application\/JSCoder
         */
        $this->registration =  new JSRegistration();
        $this->jsModules = $this->registration->get();
    }

    /**
     * called method for outputting the html Code
     *
     * @param string $jsModule name of the js module
     * @param string $options optional override of the options, only one option possible //todo perhaps extend for multiple options
     */
    public function render( $jsModule, $options = '' )
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
        $this->jsData['script'] = $this->completeScript();
        //todo create the htmlcode

        echo '<style>' . /* data of */ $this->jsData['css'] . '</style>';
        echo '<style>' . /* data of */ $this->jsData['override'] . '</style>';
        echo '<script>' . $this->jsData['script'] . '</script>';
    }

    private function completeScript()
    {
        //todo implement json encoding
        //todo implement "inside script" in "script"
        return $readyScript = $this->jsData['script'];
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
        $this->jsData['insideCode'] = $options;
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