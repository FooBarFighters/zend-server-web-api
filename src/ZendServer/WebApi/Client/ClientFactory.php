<?php

namespace FooBarFighters\ZendServer\WebApi\Client;

use FooBarFighters\ZendServer\WebApi\Client\Core\Client;
use FooBarFighters\ZendServer\WebApi\Client\Extended\Client as ExtendedClient;
use GuzzleHttp\Client as Guzzle;

class ClientFactory
{
    /**
     * @param array       $config
     * @param Guzzle|null $guzzle
     *
     * @return Client
     */
    public static function createClient(array $config, ?Guzzle $guzzle = null): Client
    {
        $guzzle = $guzzle ?: new Guzzle();
        return new Client(
            $config['baseUrl'] ?? 'https://your.zend.server.url'
            , $config['hash'] ?? ''
            , $config['username'] ?? ''
            , $config['version'] ?? ''
            , $guzzle
        );
    }

    /**
     * @param array       $config
     * @param Guzzle|null $guzzle
     *
     * @return ExtendedClient
     */
    public static function createExtendedClient(array $config, ?Guzzle $guzzle = null): ExtendedClient
    {
        return new ExtendedClient(self::createClient($config, $guzzle));
    }
}