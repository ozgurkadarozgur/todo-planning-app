<?php
/**
 * Created by PhpStorm.
 * User: ozgur
 * Date: 04.04.2020
 * Time: 23:14
 */

namespace App\Provider;


use App\Entity\Todo;

class Provider1 implements IProvider
{

    private $url = 'http://www.mocky.io/v2/5d47f24c330000623fa3ebfa';

    public function getUrl(): string
    {
        return $this->url;
    }

    public function convert2Todo(array $item): Todo
    {
        $todo = new Todo();
        $todo->setTitle($item["id"]);
        $todo->setLevel($item["zorluk"]);
        $todo->setEstimatedDuration($item["sure"]);
        return $todo;
    }
}