<?php

namespace Tests\ZendServer\WebApi\Client\Extended\WebApi\Api;

use FooBarFighters\ZendServer\WebApi\Client\Core\Client;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ClientFactory
{
    /**
     *
     * @url https://docs.guzzlephp.org/en/stable/testing.html
     *
     * @param array|null  $config
     * @param string|null $jsonFile
     * @param int|null    $responseCode
     *
     * @return Client
     */
    public static function create(?array $config, ?string $jsonFile = null, ?int $responseCode = null): Client
    {
        //== use a json file and a MockHandler to emulate a response for (unit) testing
        if($jsonFile){
            $handlerStack = HandlerStack::create(
                new MockHandler([
                    new Response($responseCode, [], file_get_contents($jsonFile)),
                ])
            );
        }

        return new Client(
            $config['baseUrl'] ?? 'https://your.zend.server.url'
            , $config['hash'] ?? ''
            , $config['username'] ?? ''
            , $config['version'] ?? ''
            , new Guzzle(['handler'=> $handlerStack])
        );
    }
}