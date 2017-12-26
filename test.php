<?php

require 'vendor/autoload.php';

use Leonis\Digiccy\Wallet;

$config = [
    'symbol' => 'BTC',
    'ip' => '192.168.2.103',
    'port' => 20621,
    'user' => 'user',
    'password' => 'password'
];

$btc = new Wallet($config);
print_r($btc->getNewAddress());
