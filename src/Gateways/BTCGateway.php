<?php

namespace Leonis\Digiccy\Gateways;

use Leonis\Digiccy\Contracts\GatewayInterface;
use Leonis\Digiccy\Traits\HttpRequest;

class BTCGateway implements GatewayInterface
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
        $response = $this->post($this->config, strtolower(__FUNCTION__), $params);
        $content  = json_decode($response->getBody()->getContents());

        return ['address' => $content->result];
    }

    //根据地址获取转账记录
    public function getTransactionsByAddress($address)
    {
        //获取该地址的账户
        $response = $this->post($this->config, 'getaccount', [$address]);
        $account  = json_decode($response->getBody()->getContents())->result;
        $response = $this->post($this->config, 'listtransactions', [$account]);
        $content  = json_decode($response->getBody()->getContents());
        //过滤掉转出的记录
        $received = $this->getReceived($content->result);

        return ['transactions' => $received];
    }

    //发送事物
    public function sendToAddress(array $params = [])
    {
        $address = $params[0];
        $value   = round($params[1], 8);
        //解锁钱包
        $this->post($this->config, 'walletlock');
        $this->post($this->config, 'walletpassphrase', [($this->config)['wallet_password'], 10]);
        $response = $this->post($this->config, 'sendtoaddress', [$address, $value, 'ucoin']);

        return ['result' => json_decode($response->getBody()->getContents())->result];
    }

    //获取钱包可用总余额
    public function getWalletBalance()
    {
        $response = $this->post($this->config, 'getbalance');

        return ['balance' => json_decode($response->getBody()->getContents())->result];
    }

    //获取地址余额包含已经转出的
    public function getAddressBalance(array $params = [])
    {
        if (strlen($params[0]) > 34 || strlen($params[0]) < 27) {
            return ['error' => 'inValid address'];
        }
        //获取该地址的账户
        $response = $this->post($this->config, 'getaccount', [$params[0]]);
        $account  = json_decode($response->getBody()->getContents())->result;
        $response = $this->post($this->config, 'getbalance', [$account]);
        $content  = json_decode($response->getBody()->getContents());

        return ['balance' => $content->result];
    }

    //过滤发送的交易
    public function getReceived(array $transactions)
    {
        $received = [];
        if (!count($transactions)) {
            return $received;
        }
        foreach ($transactions as $transaction) {
            if ($transaction->category === 'receive') {
                array_push($received, $transaction);
            }
        }

        return $received;
    }

}
