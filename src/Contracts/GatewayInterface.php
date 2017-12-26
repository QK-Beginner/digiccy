<?php

namespace Leonis\Digiccy\Contracts;

interface GatewayInterface
{
    public function getNewAddress(array $params = []);
}
