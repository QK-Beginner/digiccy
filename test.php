<?php

require 'vendor/autoload.php';

use Leonis\Digiccy\Wallet;

$config = [
    'symbol'          => 'ETH',
    'ip'              => '49.4.64.171',
    'port'            => 8545,
    'user'            => 'user',
    'password'        => 'password',
    'wallet_password' => '123456',
    'erc'             => 'gec',
];

$btc = new Wallet($config);
print_r($btc->sendToAddress(['0x93ae19169bc6f43f8806aac6248e56319c10a8e6','0x3d3b296b93a6ec7d78fcfd76a06f2b1a67fcd8af','2.1']));
