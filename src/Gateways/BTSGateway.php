<?php

namespace Leonis\Digiccy\Gateways;

use Leonis\Digiccy\Contracts\GatewayInterface;
use Leonis\Digiccy\Traits\HttpRequest;

class BTSGateway implements GatewayInterface
{
    use HttpRequest;

    protected $config;

    public function __construct(array $config)
    {

        $this->config = $config;
    }

    public function getNewAddress(array $params = [])
    {

        return ['address' => 'lbwcoin-com'];
    }

    public function getTransactionsByAddress($address)
    {
        $response = $this->rpcPost($this->config, 'get_relative_account_history', ["lbwcoin-com", 1, 100, 100]);
        $content  = json_decode($response->getBody()->getContents(), true);

        return ['transactions' => $this->dealTransactions($content['result'], $address)];
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
        //获取不可回退的区块
        $content    = json_decode($this->rpcPost($this->config, 'get_dynamic_global_properties')->getBody()->getContents());
        $unFallback = $content->result->last_irreversible_block_num;
        $received   = [];
        foreach ($transactions as $transaction) {
            if ($transaction['op']['op'][0] != 0) continue;//转账
            if ($transaction['op']['op'][1]['to'] != '1.2.467629') continue;//转入
            if ($transaction['op']['block_num'] > $unFallback) continue;//不可撤回
            if ($transaction['memo'] != $user_id) continue; //备注是否为用户id
            array_push($received, [
                'address' => $transaction['op']['op'][1]['from'], //转入的账户
                'value'   => $transaction['op']['op'][1]['amount']['amount'] / 100000,
                'hash'    => $transaction['op']['id'],
            ]);
        }

        return $received;
    }
}