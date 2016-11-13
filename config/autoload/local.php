<?php
/**
 * Local Configuration Override
 *
 * This configuration override file is for overriding environment-specific and
 * security-sensitive configuration information. Copy this file without the
 * .dist extension at the end and populate values as needed.
 *
 * @NOTE: This file is ignored from Git by default with the .gitignore included
 * in ZendSkeletonApplication. This is a good practice, as it prevents sensitive
 * credentials from accidentally being committed into version control.
 */

//cleanfix
$db = 'local';
//$db = 'server';
if ($db == 'local'){
return array(
    'db' => array(
        'username' => 'root',
        'password' => 'usbw',
    ),
);
} else {
return array(
    'db' => array(
        'username' => 'frysql',
        'password' => 'sqlfry',
    ),
);
}
