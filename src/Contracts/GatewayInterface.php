<?php

namespace Leonis\Digiccy\Contracts;

/**
 * Interface GatewayInterface
 * @package Leonis\Digiccy\Contracts
 */
interface GatewayInterface
{
    /**
     * Get a new address.
     *
     * @param array $params
     * @return mixed
     */
    public function getNewAddress(array $params = []);

    public function getTransactionsByAddress($address);

    public function getAddressBalance(array $params = []);

    public function getWalletBalance();

    public function sendToAddress(array $params = []);
}
