<?php

namespace Leonis\Digiccy\Gateways;

use Leonis\Digiccy\Contracts\GatewayInterface;
use Leonis\Digiccy\Traits\HttpRequest;

/**
 * Class BTCGateway
 * @package Leonis\Digiccy\Gateways
 */
class BTCGateway implements GatewayInterface
{
    use HttpRequest;

    /**
     * @var array
     */
    protected $config;

    /**
     * BTCGateway constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Get a new address.
     *
     * @param array $params
     * @return array|mixed
     */
    public function getNewAddress(array $params = [])
    {
        $response = $this->post($this->config, strtolower(__FUNCTION__), $params);
        $content = json_decode($response->getBody()->getContents());
        return ['address' => $content->result];
    }
}
