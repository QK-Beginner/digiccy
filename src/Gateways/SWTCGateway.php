<?php

namespace Leonis\Digiccy\Gateways;

use Leonis\Digiccy\Contracts\GatewayInterface;
use Leonis\Digiccy\Traits\HttpRequest;

class SWTCGateway implements GatewayInterface
{
    use HttpRequest;

    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getNewAddress(array $params = [])
    {
        $response = $this->get('https://api.jingtum.com/v2/wallet/new');
        $content  = json_decode($response->getBody()->getContents());

        return ['address' => $content->wallet->address, 'secret' => $content->wallet->secret];
    }

    public function getTransactionsByAddress($address)
    {
        $response = $this->get('https://api.jingtum.com/v2/accounts/' . $address . '/transactions?results_per_page=50');
        $content  = json_decode($response->getBody()->getContents());
        if (!$content->success) exit(json_encode($content));

        return ['transactions' => $this->dealTransactions($address, $content->transactions)];
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

    }

    public function dealTransactions($address, array $transactions)
    {
        $received = [];
        foreach ($transactions as $transaction) {
            //过滤掉转出的
            if ($transaction->type === 'received') {
                array_push($received, [
                    'received' => $address,
                    'value'    => $transaction->amount,
                    'hash'     => $transaction->hash,
                ]);
            }
        }

        return $received;
    }

}
