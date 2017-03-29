<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 27.11.2016
 * Time: 00:26
 */

namespace JSCoder\Utility;

use JSCoder\Interfaces\IJSCodeConfigFactory;
use Zend\View\Helper\AbstractHelper;

/**JSCoder <br>
 * View Helper
 * jsCode() returns css and js code for usage in html
 *
 * @package Application\Utility
 */
class JSCoder extends AbstractHelper
{
    private $jsModules = array();
    /** JSModule
     * @package Application\/JSCoder
     */
    private $jsData;
    
    public function __construct($config)
    {
        $this->jsModules = $config->get();
    }

    public function head ( $jsModule ){
        $this->prepare( $jsModule );

        return $this->renderHead();
    }

    /**
     * called method for outputting the html Code
     *
     * @param string $jsModule name of the js module
     * @param string $options optional override of the options, only one option possible //todo perhaps extend for multiple options
     */
    public function script( $jsModule, $options = '' )
    {
        $this->prepare( $jsModule );
        ($options !== '')?: $this->changeOptions( $options );
        
        return $this->renderScript();
    }

    private function renderScript()
    {
        $script = $this->completeScript();
        return ( ( $script ) ? '<script>' . $script . '</script>' : '');
    }

    private function renderHead(){
        $return = '';

        $return .= ( $this->jsData['jsFile'] ) ? '<script type="text/javascript" src="' . $this->jsData['jsFile'] . '"></script>' : '';
        $return .= ( $this->jsData['hasCss'] ) ? '<link href="' . $this->jsData['css'] . '" rel="stylesheet" type="text/css">' : '';
        $return .= ( $this->jsData['hasOverride'] ) ? '<link href="' . $this->jsData['override'] . '" rel="stylesheet" type="text/css">' : '';
        return $return;
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
    private function changeOptions( $options, $jsModule = 'none' )
    {
        ($jsModule == 'none')?: $this->prepare( $jsModule );
        // todo string or array depending of missing json implementation
        //meanwhile string is used
        $this->jsData['options'] = $options;
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
        dump ('this widget is not registered in or managed by JSCoder'); //todo replace dump to error trigger
    }
}