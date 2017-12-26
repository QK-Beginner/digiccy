<?php

namespace Leonis\Digiccy\Traits;

use GuzzleHttp\Client;

/**
 * Trait HttpRequest
 * @package Leonis\Digiccy\Traits
 */
trait HttpRequest
{
    /**
     * Make a post Request.
     *
     * @param array $config
     * @param $method
     * @param array $params
     * @return \Psr\Http\Message\ResponseInterface
     */
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
