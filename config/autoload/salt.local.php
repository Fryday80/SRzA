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
return array(
//    'db' => array(
//        'driver'   => 'Pdo',
//        'username' => 'root',
//        'password' => 'root',
//        'dsn'      => 'mysql:dbname=DB2836034;host=localhost:3306',
//    ),
    'db' => array(
        'driver'   => 'Pdo',
        'username' => 'root',
        'password' => 'usbw',
        'dsn'      => 'mysql:dbname=DB2836034;host=localhost:3307',
    ),
//    'db' => array(
//        'driver'   => 'Pdo_Sqlite',
//        'database' => 'srza.db',
//    ),
);