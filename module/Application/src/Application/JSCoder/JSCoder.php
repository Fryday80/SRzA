<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 27.11.2016
 * Time: 00:26
 */

namespace Application\JSCoder;

/**JSCoder <br>
 * jsCode() returns css and js code for usage in html
 *
 * @package Application\Utility
 */
class JSCoder
{
    private $jsWidgets;

    function __construct()
    {
        /**JSRegistration
         * @package Application\/JSCoder
         */
        $this->jsWidgets = (new JSRegistration())->get();
    }
    public function jsCode( $jsWidget )
    {
        $jsData = $this->getData( $jsWidget );
        $this->render( $jsData );
    }
    
    private function render($jsData)
    {
        //todo create the htmlcode
    }
    
    private function getData( $jsWidget )
    {
        $jsData = array();
        $jsWidget = $this->errorCheck( $jsWidget );

        $jsData['jsFile'] ( $this->jsWidgets[$jsWidget]['jsFile'] ) ? $this->jsWidgets[$jsWidget]['jsFile'] : '';
        
        $jsData['cssPath'] ( $this->jsWidgets[$jsWidget]['ownCss'] ) ? $this->jsWidgets[$jsWidget]['cssPath'] : '';
        $jsData['overridePath'] ( $this->jsWidgets[$jsWidget]['override'] ) ? $this->jsWidgets[$jsWidget]['overridePath'] : '';
        
        $jsData['script'] ( $this->jsWidgets[$jsWidget]['script'] ) ? $this->jsWidgets[$jsWidget]['script'] : '';
        $jsData['insideCodeValue'] ( $this->jsWidgets[$jsWidget]['insideCode'] ) ? $this->jsWidgets[$jsWidget]['insideCodeValue'] : '';
        
        return $jsData;
    }
    private function errorCheck( $jsWidget )
    {
        return ( array_key_exists($jsWidget, $this->jsWidgets) ) ? $this->jsWidgets[$jsWidget] : $this->errorMessage();
    }
    private function errorMessage()
    {
        return dumpd ('this widget is not registered in or managed by JSCoder', 'js usage error', 0); //todo replace dumpd to error trigger
    }
}