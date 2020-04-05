<?php
/**
 * Created by PhpStorm.
 * User: ozgur
 * Date: 04.04.2020
 * Time: 23:58
 */

namespace App\Provider;


class ProviderFactory
{
    public static function create($class)
    {
        return new $class();
    }
}