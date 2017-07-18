<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 18.07.2017
 * Time: 14:22
 */

namespace Application\Model\AbstractModels;


class Extras
{
    public static function get_vars($obj)
    {
        return get_object_vars($obj);
    }
}