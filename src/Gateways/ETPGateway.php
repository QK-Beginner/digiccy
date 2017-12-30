<?php

namespace Leonis\Digiccy\Gateways;

use GuzzleHttp\Client;
use Leonis\Digiccy\Contracts\GatewayInterface;
use Leonis\Digiccy\Traits\HttpRequest;

class ETPGateway implements GatewayInterface
{
    use HttpRequest;
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    //生成新地址
    public function getNewAddress(array $params = [])
    {
        $response = $this->rpcPost($this->config, 'getnewaddress', [$this->config['user'], $this->config['password']]);
        $content  = $response->getBody()->getContents();

        return ['address' => $content];
    }

    //根据地址获取转账记录
    public function getTransactionsByAddress($address)
    {
        $response = $this->rpcPost($this->config, 'fetch-history', [$address]);
        $content  = json_decode($response->getBody()->getContents());
        if (!$content->transfers) return ['transactions' => []];

        return ['transactions' => $this->dealTransactions($content->transfers, $address)];
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

    public function dealTransactions(array $transactions, $address)
    {
        $received = [];
        foreach ($transactions as $transaction) {
            array_push($received, [
                'received' => $address,
                'value'    => $transaction->value / 100000000,
                'hash'     => $transaction->received->hash,
            ]);
        }

        return $received;
    }


}
