<?php

namespace Leonis\Digiccy\Traits;

use GuzzleHttp\Client;

trait HttpRequest
{
    //bath_auth 请求
    protected function post(array $config, $method, array $params = [])
    {
        try {
            return (new Client())->post($config['ip'] . ':' . $config['port'], [
                'auth'    => [
                    $config['user'],
                    $config['password'],
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json'    => [
                    'method' => $method,
                    'params' => $params,
                ],
            ]);
        } catch (\Exception $exception) {
            exit($exception->getMessage());
        }
    }

    //普通请求
    protected function rpcPost(array $config, $method, array $params = [])
    {
        return (new Client())->post($config['ip'] . ':' . $config['port'], [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json'    => [
                'jsonrpc' => '2.0',
                'id'      => 1,
                'method'  => $method,
                'params'  => $params,
            ],
        ]);
    }

    //get请求
    protected function get($url)
    {
        try {
            return (new Client(['verify' => false]))->get($url);
        } catch (\Exception $exception) {
            exit($exception->getMessage());
        }
    }


}
