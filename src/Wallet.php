<?php

namespace Leonis\Digiccy;

class Wallet
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function getNewAddress()
    {
        return $this->gateway()->getNewAddress();
    }

    protected function gateway()
    {
        $gatewayClassName = __NAMESPACE__ . '\Gateways\\' . $this->config['symbol'] . 'Gateway';
        return new $gatewayClassName($this->config);
    }
}
