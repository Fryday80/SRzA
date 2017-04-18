<?php
    $echo = '';
    $echo .= '<script>';
    $echo .= file_get_contents ('../js/globalUsage/jquery/jquery-3.2.0.min.js');
    $echo .= file_get_contents ('../libs/globalUsage/jquery-ui/jquery-ui.min.js');
    $echo .= file_get_contents ('../js/globalUsage/popUp/popUp.js');
    $echo .= file_get_contents ('../js/globalUsage/menu/menu.js');
    $echo .= file_get_contents ('../js/globalUsage/loggingDesigner/loggingDesigner.js');
    $echo .= file_get_contents ('../js/globalUsage/accordion/accordion.js');
    $echo .= file_get_contents ('../libs/globalUsage/feedback/js/feedback.js');
    $echo .= 's</script>';
echo $echo;
