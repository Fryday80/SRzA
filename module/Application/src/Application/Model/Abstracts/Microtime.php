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
    static function dateFromMicrotime($microtime, $format = ('H:i') )
    {
        $t  = substr( $microtime, 0 , strlen( time() ) );
        return date ( $format, (int)$t );
    }
    static function addDateTime($itemArray, $format = ('H:i') )
    {
        if ( !is_array( $itemArray ) ) return null;
        foreach ( $itemArray as $item ) {
            if ( is_object( $item ) ) {
                $item->dateTime = self::dateFromMicrotime( $item->time );
            } elseif ( is_array( $item ) ){
                $item['dateTime'] = self::dateFromMicrotime( $item['time'] );
            }
        }
        return $itemArray;
    }
}