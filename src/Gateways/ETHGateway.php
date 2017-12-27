<?php

namespace Leonis\Digiccy\Gateways;

use Leonis\Digiccy\Contracts\GatewayInterface;
use Leonis\Digiccy\Traits\Help;
use Leonis\Digiccy\Traits\HttpRequest;

class ETHGateway implements GatewayInterface
{
    use HttpRequest, Help;

    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getNewAddress(array $params = [])
    {
        $wallet_password = ($this->config)['wallet_password'];
        $response        = $this->rpcPost($this->config, 'personal_newAccount', [$wallet_password]);
        $content         = json_decode($response->getBody()->getContents(), true);
        if (isset($content['error'])) return ['address' => '', 'error' => $content['error']['message']];

        return ['address' => $content['result']];
    }

    public function getTransactionsByAddress($address)
    {
        $info         = $this->getErcInfo(($this->config)['erc']);
        $transactions = $this->getErcTransactions($address, $info['contract']);

        return ['transactions' => $transactions];
    }

    public function getAddressBalance(array $params = [])
    {
        // TODO: Implement getAddressBalance() method.
    }

    public function getWalletBalance()
    {
        // TODO: Implement getWalletBalance() method.
    }

    public function sendToAddress(array $params = [])
    {
        // TODO: Implement sendToAddress() method.
    }

    public function getErcTransactions($address, $contract)
    {
        $topic0   = '0xddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef';
        $topic2   = substr($address, 2);
        $url      = "http://api.etherscan.io/api?module=logs&action=getLogs&fromBlock=0&toBlock=latest&address=" . $contract . "&topic0=" . $topic0 . "&topic0_2_opr=and&topic2=0x000000000000000000000000" . $topic2 . "&apikey=9AHCR6IJTB2H2MKBSRRZT9KHQKD1ETS8FX";
        $response = $this->get($url);

        return json_decode($response->getBody()->getContents());
    }

}
