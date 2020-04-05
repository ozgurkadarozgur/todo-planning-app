<?php
/**
 * Created by PhpStorm.
 * User: ozgur
 * Date: 05.04.2020
 * Time: 00:13
 */

namespace App\Service;


use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;

class HttpService
{
    public function get($url)
    {
        $client = HttpClient::create();

        try {
            $response = $client->request('GET', $url);
            if ($response->getStatusCode() == Response::HTTP_OK) {
                return $response->toArray();
            }
        } catch (\Exception $ex) {
            throw new \Exception($ex);
        }
    }
}