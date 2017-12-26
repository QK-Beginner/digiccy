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
    public function getNewAddress()
    {
        return $this->gateway()->getNewAddress();
    }

    /**
     * Get gateway instance.
     *
     * @return mixed
     */
    protected function gateway()
    {
        $gatewayClassName = __NAMESPACE__ . '\Gateways\\' . $this->config['symbol'] . 'Gateway';
        return new $gatewayClassName($this->config);
    }
}
