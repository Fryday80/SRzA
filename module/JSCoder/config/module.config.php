<?php

return array(
    'jsModules' => array(
        'test' => array(
            'name' => 'test',
            'jsFile' => 'TestJSFile.js',
            'script' => 'test script',
            'hasCss'=> true,
            'hasOverride'=> true,
            'css' => 'demo.css',
            'override' => 'demo override'
        ),
        'accordion' => array(
            'name' => 'accordion',
            'information' => 'part of jquery-ui',
            'script' => '$(function() {
                                                $("accordion").each(function(i, ele) {
                                                    $(ele).accordion({
                                                        heightStyle: "content",
                                                        icons: { "header": "", "activeHeader": "" }
                                                    });
                                                })
                                            });',
        ),
        'jquery' => array (
            'name' => 'jquery',
            'global' => true,

            'jsFile' => '/js/jquery.min.js',

            'hasCss' => true,
            'css' => '/libs/jquery-ui/jquery-ui.css',
        ),
        'jquery-ui' => array (
            'name' => 'jquery-ui',
            'global' => true,

            'jsFile' => '/js/jquery-ui.min.js',

            'hasCss' => true,
            'css' => '/libs/jquery-ui/jquery-ui.css',
        ),
        'pop-up' => array (
            'name' => 'pop-up',
            'information' => 'disclaimer pop up',
            'global' => true,

            'jsFile' => '/libs/popUp/popup.js',
        ),
        'simpleSlideShow' => array (
            'name' => 'simpleSlideShow',
            'information' => '',
            'global' => true,

            'jsFile' => '/libs/simpleSlideShow/simpleSlideShow.js',

            'hasCss' => true,
            'css' => '/js/jquery.min.js',
        ),
    ),

);
    // ---- example ----------------- for each js code...
    //    'jsModulename' => array(
    //        'name' => 'xy',
    //        'information' => 'part of jquery-ui',
//                'global' => true,
    //
    //        'jsFile' => 'path or "" if js permanently used e.g. jquery-ui',
    //
    //        'hasCss' => false,
    //        'css' => 'location of css',
    //
    //        'hasOverride' => false,
    //        'override' => 'string (path to override file)',
    //
    //        'script' => 'string without <script> or leave empty "" ',
    //
    //        'hasOptions' => false,
    //        'options' => 'e.g. backgroundcolor: black,',
    //    ) ),
    // ----- example end -----------------------------------------