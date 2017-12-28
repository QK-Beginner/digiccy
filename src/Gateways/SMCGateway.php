<?php

namespace Leonis\Digiccy\Gateways;

use GuzzleHttp\Client;
use Leonis\Digiccy\Contracts\GatewayInterface;

class SMCGateway implements GatewayInterface
{
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    //生成新地址
    public function getNewAddress(array $params = [])
    {
        $mainAddress = 'ACTM5hJFR1vFFYEJ35qZ7anoJTviefT5VkdB';//目前是线下测试
        return ['address' => $mainAddress . md5($params[0])];
    }

    //根据地址获取转账记录
    public function getTransactionsByAddress($address)
    {
        $requestUrl   = "http://39.106.136.195:8080/SmcRecord/" . $address;//请求扫描区块的服务器
        $client       = new Client();
        $response     = $client->request('GET', $requestUrl);
        $transactions = json_decode($response->getBody()->getContents(), true);
        foreach ($transactions as $transfer) {
            $transfer['txid'] = $transfer['tx_id'];
            unset($transfer['tx_id']);
            unset($transfer['id']);
        }
        return ['transactions' => $transactions];
    }

    //发送事物
    public function sendToAddress(array $params = [])
    {
        $this->smcRequest('wallet_unlock', ['30', 'ck123456']);
        $txid = $this->smcRequest('call_contract', ["CON2W6PbuBrGcB3EGFTK81sDfJmrMUTyXqta", "test123654", "transfer_to", "$params[0]" . '|' . "$params[1]", "ACT", "0.01"]);
        $txid = json_decode($txid->getBody()->getContents(), true)['result'];
        return ['txid'=>$txid['entry_id']] ;
    }


    //获取钱包可用总余额
    public function getWalletBalance()
    {
        //直接请求区块浏览器查询
        $url = 'https://browser.achain.com/wallets/api/browser/act/contract/balance/query/'.'ACTM5hJFR1vFFYEJ35qZ7anoJTviefT5VkdB';
        $client = new Client(['verify'=>false]);
        $response = $client->get($url);
        $balance = json_decode($response->getBody()->getContents(), true);
        return ['balance' => json_decode($balance['data'][0]['balance'])];
    }

    //获取地址余额包含已经转出的
    public function getAddressBalance(array $params = [])
    {

    }

    //过滤发送的交易
    public function getReceived(array $transactions)
    {

    }

    //发送请求
    public function smcRequest($method, array $params = [])
    {
        $config = $this->config;

        return (new Client())->post('http://' . $config['ip'] . ':' . $config['port'] . '/rpc', [
            'headers' => [
                'Content-Type'  => 'application/json',
                'Authorization' => '000000' . base64_encode($config['user'] . ':' . $config['password']),
            ],
            'json'    => [
                'method' => $method,
                'params' => $params,
                'id'=>'1'
            ],

        ]);
    }

}
