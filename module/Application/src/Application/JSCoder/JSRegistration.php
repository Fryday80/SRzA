<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 26.03.2017
 * Time: 13:22
 */

namespace Application\JSCoder;

Class JSRegistration
{
    public $lib;
    
    public function __construct()
    {
        $this->registration();
    }
    public function get()
    {
        return $this->lib;
    }
    private function registration()
    {
    $this->lib[]= new JSModule (array(
        'name' => 'accordion',
        'jsFile' => '/libs/ckeditor/ckeditor.js',
        
        'ownCss' => true,
        'cssPath' => '/libs/ckeditor/content.css',
        
        'override' => false,
        'overridePath' => 'string (path to override file)',
        
        'script' => '$(function() {
                                    $("accordion").each(function(i, ele) {
                                        $(ele).accordion({
                                            heightStyle: "content",
                                            icons: { "header": "", "activeHeader": "" }
                                        });
                                    })
                                });',
        
        'insideCode' => false,
        'insideCodeValue' => 'todo',
    ),
//        for each js code...
//    $this->lib[]= new JSModul (array(
//        'name' => 'xy',
//        'jsFile' => 'path',

//        'ownCss' => true,
//        'cssPath' => 'location of css',

//        'override' => true,
//        'overridePath' => 'string (path to override file)',

//        'code' => 'string without <script> or empty "" ',

//        'insideCode' => true,
//        'insideCodeValue' => 'e.g. backgroundcolor: black,',
//    ),
    );
    }
}