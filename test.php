<?php

require 'vendor/autoload.php';

use Leonis\Digiccy\Wallet;

$config = [
    'symbol'          => 'CPL',
    'type'            => 3,
    'ip'              => '39.104.59.128',
    'port'            => 8890,
    'url'             => 'rpc',
    'user'            => 'root',
    'password'        => 'root123456',
    'wallet_password' => '123456',
];

$btc = new Wallet($config);
//print_r($btc->getNewAddress(['11']));die;
print_r($btc->getTransactionsByAddress('1'));
