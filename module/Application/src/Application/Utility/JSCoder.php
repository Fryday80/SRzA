<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 27.11.2016
 * Time: 00:26
 */

namespace Application\Utility;


use ZendDiagnosticsTest\TestAsset\Check\ReturnThis;

/**JSCoderTablehelperConfig <br>
 * jsCode() returns css and js code for usage in html
 *
 * @package Application\Utility
 */
class JSCoder
{
    private $jsWidgets;

    function __construct()
    {
        $this->jsWidgets = $this->getRegistred();
    }
    function jsCode( $jsWidget )
    {
        $this->errorCheck( $jsWidget );

        echo ($this->jsWidgets[$jsWidget]['css'] == '') ? '' : $this->jsWidgets[$jsWidget]['css'];
        echo ($this->jsWidgets[$jsWidget]['jsFile'] == '') ? '' : $this->jsWidgets[$jsWidget]['jsFile'];
        echo ($this->jsWidgets[$jsWidget]['script'] == '') ? '' : $this->jsWidgets[$jsWidget]['script'];
    }
    private function getRegistred() {
        $this->jsWidgets = array(
            'accordion' => array (
                'css' => function() {$this->headLink()->prependStylesheet('/libs/jgallery/css/font-awesome.min.css');},
                'jsFile' => function() {$this->headScript()->appendFile('/libs/jgallery/js/tinycolor-0.9.16.min.js');},
                'script' => '<script>
                                $(function() {
                                    $("accordion").each(function(i, ele) {
                                        $(ele).accordion({
                                            heightStyle: "content",
                                            icons: { "header": "", "activeHeader": "" }
                                        });
                                    })
                                });
                            </script>',
            ),
            'ckeditor' => array (
                'css' => function() {$this->headLink()->prependStylesheet('/libs/ckeditor/content.css');},
                'jsFile' => function() {$this->headScript()->appendFile('/libs/ckeditor/ckeditor.js');},
                'script' => '',
            ),
            'example' => array (
                'css' => function() {$this->headLink()->prependStylesheet('path to css file');},
                'jsFile' => function() {$this->headScript()->appendFile('path to css file');},
                'script' => '',
            ),
        );
    }
    private function errorCheck( $jsWidget )
    {
        return ( array_key_exists($jsWidget, $this->jsWidgets) ) ? : $this->errorMessage();
    }
    private function errorMessage()
    {
        dumpd ('this widged is not regitered in or managed by JSCoder', 'js usage error', 0);
    }
}