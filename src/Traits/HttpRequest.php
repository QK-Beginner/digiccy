<?php

namespace Leonis\Digiccy\Traits;

use GuzzleHttp\Client;

trait HttpRequest
{
    protected function post(array $config, $method, array $params)
    {
        return (new Client())->post($config['ip'] . ':' . $config['port'], [
            'auth' => [
                $config['user'],
                $config['password']
            ],
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'method' => $method,
                'params' => $params
            ]
        ]);
    }
}
