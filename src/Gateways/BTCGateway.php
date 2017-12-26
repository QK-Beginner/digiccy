<?php

namespace Leonis\Digiccy\Gateways;

use Leonis\Digiccy\Contracts\GatewayInterface;
use Leonis\Digiccy\Traits\HttpRequest;

class BTCGateway implements GatewayInterface
{
    use HttpRequest;

    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getNewAddress(array $params = [])
    {
        $response = $this->post($this->config, strtolower(__FUNCTION__), $params);
        $content = json_decode($response->getBody()->getContents());
        return ['address' => $content->result];
    }
}
