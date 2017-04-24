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
    static function addDateTime($itemArray, $format = ('H:i') )
    {
        if ( !is_array( $itemArray ) ) return null;
        foreach ( $itemArray as $item ) {
            if ( is_object( $item ) ) {
                $item->dateTime = date($format, $item->time );
            } elseif ( is_array( $item ) ){
                $item['dateTime'] = date($format, $item['time'] );
            }
        }
        return $itemArray;
    }
}