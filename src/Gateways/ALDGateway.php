<?php

namespace Leonis\Digiccy\Gateways;

use GuzzleHttp\Client;
use Leonis\Digiccy\Contracts\GatewayInterface;
use Leonis\Digiccy\Traits\HttpRequest;

class ALDGateway implements GatewayInterface
{
    use HttpRequest;
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    //生成新地址
    public function getNewAddress(array $params = [])
    {
        return ['address' => 'uncoinex.com'];
    }

    //根据地址获取转账记录
    public function getTransactionsByAddress($userId)
    {
        $result = $this->rpcPost($this->config, 'get_account_history', ['uncoinex.com',10000]);
        $result = json_decode($result->getBody()->getContents(), true);
        $unBackBlock = json_decode($this->rpcPost($this->config,'get_dynamic_global_properties',[])->getBody()->getContents(),true);
        $unBackBlock = $unBackBlock['result']['last_irreversible_block_num'];
        $received   = [];
        foreach ($result['result'] as $item){
            if ($item['memo'] != $userId){
                continue; //备注是否为用户id
            }
            if ($item['op']['op'][0] != 0){
                continue;//转账
            }
            if ($item['op']['op'][1]['to'] != '1.2.76144'){
                continue;//转入
            }
            if($item['op']['op'][1]['amount']['asset_id']!=='1.3.6'){
                continue;//转入的不是ald
            }
            if ($item['op']['block_num'] > $unBackBlock){
                continue;//不可回撤;
            }
            // 根据ID 判断该交易所在的交易块位置
            $object = $this->rpcPost($this->config,'get_object',[$item['op']['id']])->getBody()->getContents();
            $object = json_decode($object,true);
            if($object['result'][0]['op'][1]['to']!='1.2.76144'){
                continue;//不可撤回
            }
            $trx_in_block = $object['result'][0]['trx_in_block'];
            /*判断区块里的交易*/
            $block = $this->rpcPost($this->config,'get_block',[$object['result'][0]['block_num']])->getBody()->getContents();
            $block = json_decode($block,true);
            if($block['result']['transactions'][$trx_in_block]['operations'][0][1]['to']!='1.2.76144'){
                continue;
            }
            array_push($received,[
                'address' => $item['op']['op'][1]['from'], //转入的账户
                'value'   => $item['op']['op'][1]['amount']['amount'] / 100000,
                'hash'    => $item['op']['id'],
            ]);
        }
        return ['transactions' => $received];
    }

    //发送事物
    public function sendToAddress(array $params = [])
    {
        $response = $this->rpcPost($this->config, 'transfer',["uncoinex.com","$params[0]","$params[1]","ALD","$params[2]","true","0.12501","ALD"]);
        $result = json_decode($response->getBody()->getContents(),true);
        if($result){
            sleep(2);
            $txid = $this->dealTransactionsOut($params[2]);
            return ['txid'=>$txid];
        }
    }

    //获取钱包可用总余额
    public function getWalletBalance()
    {
        $response = $this->rpcPost($this->config,'list_account_balances',['uncoinex.com']);
        $result = json_decode($response->getBody()->getContents(),true);
        return ['balance'=>$result['result'][0]['amount']/100000];
    }

    //获取地址余额包含已经转出的
    public function getAddressBalance(array $params = [])
    {

    }

    //过滤发送的交易
    public function getReceived(array $transactions)
    {

    }

    //过滤出转出交易哈希ID
    /*
     * $token  转账时的备注
     * return $tx_id
     * */
    public function dealTransactionsOut($token)
    {
        $result = $this->rpcPost($this->config, 'get_relative_account_history', ['uncoinex.com', 1, 10000, 10000]);
        $result = json_decode($result->getBody()->getContents(), true);
        foreach ($result['result'] as $item){
            if($item['memo']==$token){
                return $item['op']['id'];
                break;
            }
        }
    }

}
