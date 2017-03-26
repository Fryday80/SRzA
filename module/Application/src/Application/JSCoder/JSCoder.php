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
     */
    public function render( $jsModule, $options = '' )
    {
        $this->getData( $jsModule );
        ($options !== '')?: $this->changeOptions( $options );

        $this->renderData();
    }
    
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
     * change given options / "insideCode"
     * @param $options options
     * @param string $jsModule selection of the js module
     */
    public function changeOptions( $options, $jsModule = 'none' )
    {
        ($jsModule == 'none')?: $this->getData( $jsModule );
        // todo string or array depending of missing json implementation
        //meanwhile string is used
        $this->jsData['insideCode'] = $options;
    }
    
    private function getData( $jsModule )
    {
        $jsModule = $this->errorCheck( $jsModule );

        $this->jsData['jsFile'] ( $this->jsModules[$jsModule]['jsFile'] ) ? $this->jsModules[$jsModule]['jsFile'] : '';
        
        $this->jsData['cssPath'] ( $this->jsModules[$jsModule]['ownCss'] ) ? $this->jsModules[$jsModule]['cssPath'] : '';
        $this->jsData['overridePath'] ( $this->jsModules[$jsModule]['override'] ) ? $this->jsModules[$jsModule]['overridePath'] : '';
        
        $this->jsData['script'] ( $this->jsModules[$jsModule]['script'] ) ? $this->jsModules[$jsModule]['script'] : '';
        $this->jsData['insideCodeValue'] ( $this->jsModules[$jsModule]['insideCode'] ) ? $this->jsModules[$jsModule]['insideCodeValue'] : '';
    }
    
    private function errorCheck( $jsModule )
    {
        return ( array_key_exists($jsModule, $this->jsModules) ) ? $this->jsModules[$jsModule] : $this->errorMessage();
    }
    private function errorMessage()
    {
        return dumpd ('this widget is not registered in or managed by JSCoder', 'js usage error', 0); //todo replace dumpd to error trigger
    }
}