<?php

namespace Leonis\Digiccy\Gateways;

use Leonis\Digiccy\Contracts\GatewayInterface;
use Leonis\Digiccy\Traits\HttpRequest;

class IPTGateway implements GatewayInterface
{
    use HttpRequest;

    protected $config;

    public function __construct(array $config)
    {

        $this->config = $config;
    }

    public function getNewAddress(array $params = [])
    {
        $response = $this->get('http://39.106.136.195/getAddress/' . $params[0]);
        $content  = json_decode($response->getBody()->getContents());

        return ['address' => $content->result];
    }

    public function getTransactionsByAddress($address)
    {
        $response = $this->get('http://39.106.136.195/getTransfers/' . $address);
        $content  = json_decode($response->getBody()->getContents());

        return ['transactions' => $this->dealTransactions($content->result)];
    }

    public function getAddressBalance(array $params = [])
    {

    }

    public function getWalletBalance()
    {
        // TODO: Implement getWalletBalance() method.
    }

    public function sendToAddress(array $params = [])
    {

    }

    public function dealTransactions(array $transactions)
    {
        $received = [];
        foreach ($transactions as $transaction) {
            array_push($received, [
                'received' => $transaction->address,
                'value'    => $transaction->value,
                'hash'     => $transaction->hash,
            ]);
        }

        return $received;
    }

}
