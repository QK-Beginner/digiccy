<?php

namespace Leonis\Digiccy\Gateways;

use Leonis\Digiccy\Contracts\GatewayInterface;
use Leonis\Digiccy\Traits\HttpRequest;

class SANCGateway implements GatewayInterface
{
    use HttpRequest;

    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getNewAddress(array $params = [])
    {
        $response = $this->get('https://cw.sanchain.org/api/wallet/generate_wallet');
        $content  = json_decode($response->getBody()->getContents());

        return ['address' => $content->result->address, 'secret' => $content->result->seed];
    }

    public function getTransactionsByAddress($address)
    {
        $response = $this->get('http://39.106.115.48/getTransferByAddress/' . $address);
        $content  = json_decode($response->getBody()->getContents());

        return ['transactions' => $this->dealTransactions($content->result)];
    }

    public function getAddressBalance(array $params = [])
    {
        $response = $this->get('https://cw.sanchain.org/api/wallet/balance?address=' . $params[0]);

        return ['balance' => json_decode($response->getBody()->getContents())->result->balance];
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
        $trans = [];
        foreach ($transactions as $transaction) {
            array_push($trans, [
                'received' => $transaction->destination,
                'value'    => $transaction->amount / 1000000,
                'hash'     => $transaction->hash,
            ]);
        }

        return $trans;
    }

}
