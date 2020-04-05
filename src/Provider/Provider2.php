<?php
/**
 * Created by PhpStorm.
 * User: ozgur
 * Date: 04.04.2020
 * Time: 23:16
 */

namespace App\Provider;


use App\Entity\Todo;

class Provider2 implements IProvider
{

    private $url = 'http://www.mocky.io/v2/5d47f235330000623fa3ebf7';

    public function getUrl(): string
    {
        return $this->url;
    }

    public function convert2Todo(array $item): Todo
    {
        $title = array_keys($item)[0];
        $level = $item[$title]["level"];
        $duration = $item[$title]["estimated_duration"];

        $todo = new Todo();
        $todo->setTitle($title);
        $todo->setLevel($level);
        $todo->setEstimatedDuration($duration);
        return $todo;
    }
}