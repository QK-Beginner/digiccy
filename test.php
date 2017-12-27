<?php

require 'vendor/autoload.php';

use Leonis\Digiccy\Wallet;

$config = [
    'symbol'          => 'ETH',
    'erc'             => 'six',
    'ip'              => '49.4.64.171',
    'port'            => 8545,
    'user'            => 'user',
    'password'        => 'password',
    'wallet_password' => '123456',
];

$btc = new Wallet($config);
print_r($btc->getTransactionsByAddress('0xd2f87e219d7f94ecb89b2603d32ac2e618e634ca'));
