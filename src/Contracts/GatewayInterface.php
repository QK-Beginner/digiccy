<?php

namespace Leonis\Digiccy\Contracts;

interface GatewayInterface
{
    public function getNewAddress(array $params = []);

    public function getTransactionsByAddress($address);

    public function getAddressBalance(array $params = []);

    public function getWalletBalance();

    public function sendToAddress(array $params = []);
}
