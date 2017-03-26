<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 26.03.2017
 * Time: 13:22
 */

namespace Application\JSCoder;

/**
 * Class JSRegistration
 *
 * enter data for used js modules/snippets here
 *
 * usage in JSCoder.php == class JSCoder
 *
 * @package Application\JSCoder
 */
Class JSRegistration
{
    public $lib;
    
    public function __construct()
    {
        $this->registration();
    }

    /**
     * method to get $this->lib
     * @return array $this->lib array of JSModule objects
     */
    public function get()
    {
        return $this->lib;
    }

    /**
     * enter data here
     * out-commented example appended
     */
    private function registration()
    {
    $this->lib[] = new JSModule (array(
        'name' => 'accordion',
        'jsFile' => '/libs/ckeditor/ckeditor.js',
        
        'hasCss' => true,
        'css' => '/libs/ckeditor/content.css',
        
        'hasOverride' => false,
        'override' => 'string (path to override file)',
        
        'script' => '$(function() {
                                    $("accordion").each(function(i, ele) {
                                        $(ele).accordion({
                                            heightStyle: "content",
                                            icons: { "header": "", "activeHeader": "" }
                                        });
                                    })
                                });',
        
        'hasOptions' => false,
        'options' => 'todo',
    )
// ---- example ----------------- for each js code...
//    $this->lib[]= new JSModul (array(
//        'name' => 'xy',
//        'jsFile' => 'path',

//        'hasCss' => true,
//        'css' => 'location of css',

//        'hasOverride' => true,
//        'override' => 'string (path to override file)',

//        'script' => 'string without <script> or empty "" ',

//        'hasOptions' => true,
//        'options' => 'e.g. backgroundcolor: black,',
//    ),
// ----- example end -----------------------------------------
    );
    }

    /**
     * Change the standard settings of a js module
     * @param string $jsModule the module name as given
     * @param mixed $settings the settings
     * @param string $setting the option to change. standard is "options"
     */
    public function setNewStandardSettings($jsModule, $settings, $setting = 'options')
    {
        $i = 0;
        while ($this->lib[$i])
        {
            ($this->lib[$i]['name'] !== $jsModule)?: $this->lib[$i]->setNewStandardSettings($settings, $setting);
            $i++;
        }
    }
}