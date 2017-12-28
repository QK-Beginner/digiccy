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
        if ($this->config['erc']) {//erc资产
            $info = $this->getErcInfo($this->config['erc']);
            if (!$info) {
                exit('no symbol config');
            }
            $transactions = $this->getErcTransactions($address, $info['contract']);
        } else {//以太坊
            $transactions = $this->getEthTransactions($address);
        }

        return ['transactions' => $transactions];
    }

    public function getAddressBalance(array $params = [])
    {
        $address = $params[0];
        if ($this->config['erc']) {//erc资产
            $info = $this->getErcInfo($this->config['erc']);
            if (!$info) {
                exit('no symbol config');
            }
            $url = 'https://api.etherscan.io/api?module=account&action=tokenbalance&contractaddress=' . $info['contract'] . '&address=' . $address . '&tag=latest&apikey=YourApiKeyToken';
        } else {//以太坊
            $url = 'https://api.etherscan.io/api?module=account&action=balance&address=' . $address . '&tag=latest&apikey=YourApiKeyToken';
        }

        return ['balance' => json_decode($this->get($url)->getBody()->getContents())->result];
    }

    public function getWalletBalance()
    {
        // TODO: Implement getWalletBalance() method.
    }

    public function sendToAddress(array $params = [])
    {
        if ($this->config['erc']) {//erc资产
            $info = $this->getErcInfo($this->config['erc']);
            if (!$info) {
                exit('no symbol config');
            }

            //发送erc20资产
            return $this->sendErc($info, $params);
        } else {//以太坊
            //发送eth
            return $this->sendEth();
        }
    }

    public function getErcTransactions($address, $contract)
    {
        $topic0   = '0xddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef';
        $topic2   = substr($address, 2);
        $url      = "http://api.etherscan.io/api?module=logs&action=getLogs&fromBlock=0&toBlock=latest&address=" . $contract . "&topic0=" . $topic0 . "&topic0_2_opr=and&topic2=0x000000000000000000000000" . $topic2 . "&apikey=9AHCR6IJTB2H2MKBSRRZT9KHQKD1ETS8FX";
        $response = $this->get($url);

        return json_decode($response->getBody()->getContents());
    }

    public function getEthTransactions($address)
    {
        $url      = 'http://api.etherscan.io/api?module=account&action=txlist&address=' . $address . '&startblock=0&endblock=lastest&sort=desc&apikey=9AHCR6IJTB2H2MKBSRRZT9KHQKD1ETS8FX';
        $response = $this->get($url);
        $content  = json_decode($response->getBody()->getContents());

        //过滤掉转出的记录
        return $this->getImportEthTransactions($address, $content->result);
    }

    protected function getImportEthTransactions($address, $transactions)
    {
        $received = [];
        if (!count($transactions)) {
            return $received;
        }
        foreach ($transactions as $transaction) {
            if ($transaction->to === $address) {
                array_push($received, $transaction);
            }
        }

        return $received;
    }

    protected function sendErc(array $info, array $params)
    {
        $from    = $params[0];
        $receive = $params[1];
        $value   = $params[2];

        //初始化数字格式
        $number = number_format($value * pow(10, $info['decimal']), 0, '', '');
        $url    = 'http://39.106.136.195:3000/index?dec=' . $number;//多位数转16进制接口
        $hex    = json_decode($this->get($url)->getBody()->getContents())->hex;
        if (!$hex) {
            exit('fail get hex');
        }
        //解锁账户
        $this->rpcPost($this->config, 'personal_unlockAccount', [$from, $this->config['wallet_password']]);
        //发送
        $receive = substr($receive, 2);
        $value   = str_pad($hex, 64, '0', STR_PAD_LEFT);
        $data    = '0xa9059cbb000000000000000000000000' . $receive . $value;

        $response = $this->rpcPost($this->config, 'eth_sendTransaction', [[
            'from'  => $from,
            'to'    => $info['contract'],//合约地址
            //'gasPrice' => '0x4e3b29200',//燃气费
            'value' => '0x0',
            'data'  => $data,
        ]]);
        $content  = $response->getBody()->getContents();
        if (isset(json_decode($content, true)['error'])) exit($content);

        return json_decode($content)->result;
    }

    protected function sendEth()
    {

    }


}
