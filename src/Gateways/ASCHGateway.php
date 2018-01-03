<?php

namespace Leonis\Digiccy\Gateways;

use Leonis\Digiccy\Contracts\GatewayInterface;
use Leonis\Digiccy\Traits\HttpRequest;

class ASCHGateway implements GatewayInterface
{
    use HttpRequest;

    protected $config;

    public function __construct(array $config)
    {

        $this->config = $config;
    }

    public function getNewAddress(array $params = [])
    {
        $response = $this->get('http://39.104.13.117:8192/api/accounts/new');
        $content  = json_decode($response->getBody()->getContents());

        return ['address' => $content->address, 'secret' => $content->secret];
    }

    public function getTransactionsByAddress($address)
    {
        $url     = 'http://39.104.13.117:8192/api/transactions?recipientId=' . $address . '&orderBy=t_timestamp:desc&limit=100';
        $content = json_decode($this->get($url)->getBody()->getContents());

        return ['transactions' => $this->dealTransactions($content->transactions)];
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
                'address' => $transaction->recipientId, //转入的账户
                'value'   => $transaction->amount / 100000000,
                'hash'    => $transaction->id,
            ]);
        }

        return $received;
    }
}