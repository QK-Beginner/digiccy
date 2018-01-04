<?php

namespace Leonis\Digiccy\Gateways;

use GuzzleHttp\Client;
use Leonis\Digiccy\Contracts\GatewayInterface;

class ACTGateway implements GatewayInterface
{
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    //生成新地址
    public function getNewAddress(array $params = [])
    {
        //先解锁钱包
        $response = $this->actRequest('', $params);
        $content  = json_decode($response->getBody()->getContents());

        return ['address' => $content->result];
    }

    //根据地址获取转账记录
    public function getTransactionsByAddress($address)
    {

    }

    //发送事物
    public function sendToAddress(array $params = [])
    {

    }

    //获取钱包可用总余额
    public function getWalletBalance()
    {

    }

    //获取地址余额包含已经转出的
    public function getAddressBalance(array $params = [])
    {

    }

    //过滤发送的交易
    public function getReceived(array $transactions)
    {

    }

    //发送请求
    public function actRequest($method, array $params = [])
    {
        $config = $this->config;
        try {
            return (new Client())->post('http::/' . $config['ip'] . ':' . $config['port'] . '/rpc', [
                'headers' => [
                    'Content-Type'  => 'application/json',
                    'Authorization' => '000000' . base64_encode($config['user'] . ':' . $config['password']),
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

}
