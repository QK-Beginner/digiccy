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
    protected function post(array $config, $method, array $params = [])
    {
        try {
            return (new Client())->post($config['ip'] . ':' . $config['port'] . '/' . $config['url'], [
                'auth'    => [
                    $config['user'],
                    $config['password'],
                ],
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
        } catch (\Exception $exception) {
            exit($exception->getMessage());
        }
    }

    //普通请求
    protected function rpcPost(array $config, $method, array $params = [])
    {
        try {
            return (new Client())->post($config['ip'] . ':' . $config['port'] . '/' . $config['url'], [
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
        } catch (\Exception $exception) {
            exit($exception->getMessage());
        }
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
