<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 23.04.2017
 * Time: 19:56
 */

namespace Application\Model\Enums;


abstract class ActionType {
    const PAGE_CALL = 0;
    const ERROR = 1;
    const NOT_ALLOWED = 2;

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
        if ( $type == ActionType::NOT_ALLOWED )
            return 'requested resource ist not allowed for this user';
    }
}