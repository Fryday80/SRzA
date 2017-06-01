<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 23.04.2017
 * Time: 19:56
 */

namespace Application\Model\Abstracts;


abstract class ActionType {
    const PAGE_CALL = 0;
    const ERROR = 1;

    /**
     * Translates the constants of ActionType abstract class to string
     * @param $type
     * @return string
     */
    static function translator($type){
        if ( $type == ActionType::PAGE_CALL )
            return 'Page Call';
        if ( $type == ActionType::ERROR )
            return 'Error';
    }
}