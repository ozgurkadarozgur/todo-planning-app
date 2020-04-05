<?php
/**
 * Created by PhpStorm.
 * User: ozgur
 * Date: 04.04.2020
 * Time: 23:12
 */

namespace App\Provider;


use App\Entity\Todo;

interface IProvider
{
    public function getUrl() : string;

    public function convert2Todo(array $item) : Todo;
}