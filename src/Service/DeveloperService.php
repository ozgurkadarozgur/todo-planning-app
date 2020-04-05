<?php
/**
 * Created by PhpStorm.
 * User: ozgur
 * Date: 05.04.2020
 * Time: 13:30
 */

namespace App\Service;


class DeveloperService
{
    public static $arr = [
        [
            'name' => 'DEV1',
            'level' => 1,
        ],
        [
            'name' => 'DEV2',
            'level' => 2,
        ],
        [
            'name' => 'DEV3',
            'level' => 3,
        ],
        [
            'name' => 'DEV4',
            'level' => 4,
        ],
        [
            'name' => 'DEV5',
            'level' => 5,
        ],
    ];

    public static $todo_by_developer_arr = [
        'DEV1' => [],
        'DEV2' => [],
        'DEV3' => [],
        'DEV4' => [],
        'DEV5' => [],
    ];

    public static $matched_jobs = [];
}