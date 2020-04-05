<?php
/**
 * Created by PhpStorm.
 * User: ozgur
 * Date: 04.04.2020
 * Time: 23:17
 */

namespace App\Service;


use App\Provider\IProvider;
use App\Provider\Provider1;
use App\Provider\Provider2;
use App\Provider\ProviderFactory;
use App\Repository\TodoRepository;

class ProviderService
{

    private $provider_arr = [
        Provider1::class,
        Provider2::class,
    ];

    private $todoRepository;
    private $httpService;

    public function __construct(TodoRepository $todoRepository, HttpService $httpService)
    {
        $this->todoRepository = $todoRepository;
        $this->httpService = $httpService;
    }

    public function fetch_data()
    {
        foreach ($this->provider_arr as $provider) {
            $instance = ProviderFactory::create($provider);
            if ($instance instanceof IProvider) {
                $response = $this->httpService->get($instance->getUrl());
                if ($response) {
                    foreach ($response as $item) {
                        $todo = $instance->convert2Todo($item);
                        if ($todo) {
                            $this->todoRepository->create($todo);
                        }
                    }
                }
            } else throw new \Exception("Expected:IProvider, found:".get_class($instance));
        }
    }

}