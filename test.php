<?php

require 'vendor/autoload.php';

use Leonis\Digiccy\Wallet;

$config = [
    'symbol'          => 'SMC',
    'ip'              => '127.0.0.1',
    'port'            => 8299,
    'user'            => 'user',
    'password'        => 'password',
    'type'            => '3'
//    'wallet_password' => '123456',
//    'erc'             => 'gec22',
];

$btc = new Wallet($config);
print_r($btc->getWalletBalance());
