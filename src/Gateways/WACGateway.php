<?php

namespace Leonis\Digiccy\Gateways;

use GuzzleHttp\Client;
use Leonis\Digiccy\Contracts\GatewayInterface;
use Leonis\Digiccy\Traits\HttpRequest;

class WACGateway implements GatewayInterface
{
    use HttpRequest;

    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getNewAddress(array $params = [])
    {
        $response = $this->get('https://node02.wearechain.com/tx/register-address');
        $content  = json_decode($response->getBody()->getContents());

        return ['address' => $content->data->publicKey, 'secret' => $content->data->privateKey];
    }

    public function getTransactionsByAddress($address)
    {
        $response = $this->get('http://39.106.136.195:82/getTransfers/' . $address);
        $content  = json_decode($response->getBody()->getContents());

        return ['transactions' => $this->dealTransactions($content->transfers)];
    }

    public function getAddressBalance(array $params = [])
    {
        //TODO::获取余额
    }

    public function getWalletBalance()
    {
        // TODO: Implement getWalletBalance() method.
    }

    public function sendToAddress(array $params = [])
    {
        $client   = new Client(['verify' => false]);
        $response = $client->request('POST', 'https://node02.wearechain.com/tx/send', [
            'json' => [
                'from'        => $params['from'],
                'to'          => $params['to'],
                'private_key' => $params['key'],
                'amount'      => $params['amount'],
                'fee'         => '0.1',
            ],
        ]);
        $content  = json_decode($response->getBody()->getContents());
        if (!$content->status) {
            return ['txid' => '', 'message' => $content->msg];
        }

        return ['txid' => $content->data];
    }

    public function dealTransactions(array $transactions)
    {
        $received = [];
        foreach ($transactions as $transaction) {
            //过滤掉转出的
            array_push($received, [
                'received' => $transaction->address,
                'value'    => $transaction->money,
                'hash'     => $transaction->hash,
            ]);

        }

        return $received;
    }

}
