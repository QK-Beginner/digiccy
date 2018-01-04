<?php

namespace Leonis\Digiccy;

/**
 * Class Wallet
 * @package Leonis\Digiccy
 */
class Wallet
{
    /**
     * @var
     */
    protected $config;

    /**
     * Wallet constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Get a new address.
     *
     * @return mixed
     */
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

    /**
     * Get gateway instance.
     *
     * @return mixed
     */
    protected function gateway()
    {
        if ($this->config['type'] == 1) {//btc
            $gatewayClassName = __NAMESPACE__ . '\Gateways\BTCGateway';
        } elseif ($this->config['type'] == 2) {//eth
            $gatewayClassName = __NAMESPACE__ . '\Gateways\ETHGateway';
        } else {
            $gatewayClassName = __NAMESPACE__ . '\Gateways\\' . $this->config['symbol'] . 'Gateway';
        }

        return new $gatewayClassName($this->config);
    }
}
