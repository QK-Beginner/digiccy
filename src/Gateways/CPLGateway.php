<?php

namespace Leonis\Digiccy\Gateways;

use Leonis\Digiccy\Contracts\GatewayInterface;
use Leonis\Digiccy\Traits\HttpRequest;

class CPLGateway implements GatewayInterface
{
    use HttpRequest;

    protected $config;

    public function __construct(array $config)
    {

        $this->config = $config;
    }

    public function getNewAddress(array $params = [])
    {
        $this->post($this->config, 'wallet_unlock', ["60", "lbwcoin123"]);
        $this->post($this->config, 'wallet_account_create', ['user' . $params[0]]);
        $response = $this->post($this->config, 'wallet_get_account_public_address', ['user' . $params[0]]);
        $content  = json_decode($response->getBody()->getContents());

        return ['address' => $content->result];
    }

    public function getTransactionsByAddress($user_id)
    {
        //$this->post($this->config, 'wallet_unlock', ['60', 'lbwcoin123']);
        $transfers = $this->post($this->config, 'wallet_account_transaction_history', ['user' . $user_id]);
        $content   = json_decode($transfers->getBody()->getContents());

        return ['transactions' => $this->dealTransactions($content->result, $user_id)];
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

    public function dealTransactions(array $transactions, $user_id)
    {
        $received = [];
        foreach ($transactions as $transaction) {
            if ($transaction->ledger_entries[0]->to_account === 'user' . $user_id) {
                array_push($received, [
                    'address' => $transaction->ledger_entries[0]->to_account, //转入的账户
                    'value'   => $transaction->ledger_entries[0]->amount->amount / 100000,
                    'hash'    => $transaction->trx_id,
                ]);
            }
        }

        return $received;
    }
}