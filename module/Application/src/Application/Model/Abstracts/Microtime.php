<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 23.04.2017
 * Time: 19:59
 */

namespace Application\Model\Abstracts;


abstract class Microtime
{
    static function dateFromMicrotime($microtime, $format = ('H:i')){
        $t  = substr($microtime, 0 , strlen(time()));
        return date ($format, (int)$t);
    }
}