<?php

namespace Leonis\Digiccy;

class Wallet
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    //获取地址
    public function getNewAddress(array $params = [])
    {
        return $this->gateway()->getNewAddress($params);
    }

    //根据地址获取交易事物
    public function getTransactionsByAddress($address)
    {
        return $this->gateway()->getTransactionsByAddress($address);
    }

    //根据地址获取账户余额
    public function getAddressBalance(array $params = [])
    {
        return $this->gateway()->getAddressBalance($params);
    }

    //获取钱包总余额
    public function getWalletBalance()
    {
        return $this->gateway()->getWalletBalance();
    }

    //发送交易
    public function sendToAddress(array $params = [])
    {
        return $this->gateway()->sendToAddress($params);
    }

    protected function gateway()
    {
        $gatewayClassName = __NAMESPACE__ . '\Gateways\\' . $this->config['symbol'] . 'Gateway';

        return new $gatewayClassName($this->config);
    }
}
