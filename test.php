<?php

require 'vendor/autoload.php';

use Leonis\Digiccy\Wallet;

$config = [
    'symbol'          => 'BTC',
    'ip'              => '127.0.0.1',
    'port'            => 20621,
    'user'            => 'user',
    'password'        => 'password',
    'wallet_password' => 'root',
];

$btc = new Wallet($config);
print_r($btc->sendToAddress(['LMitmobUKgkx9VpZZnDs8hM1ERo6awEFQV', '0.1']));
