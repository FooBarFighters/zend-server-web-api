<?php

namespace Tests\ZendServer\WebApi;

use FooBarFighters\ZendServer\WebApi\Client\ApiClientInterFace;
use FooBarFighters\ZendServer\WebApi\Client\ClientFactory;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use RuntimeException;

class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var false|string
     */
    protected $responseDir;

    /**
     * @var string
     */
    protected $rootDir;

    /**
     *
     */
    public function setUp(): void
    {
        $this->rootDir = dirname(__DIR__, 2);
        $this->responseDir = realpath(__DIR__ . '/responses');
    }

    /**
     * Return a custom Guzzle client, for instance to use for mocking an API response.
     *
     * @param string|null $jsonFile
     *
     * @return Guzzle
     */
    public static function getGuzzleClient(string $jsonFile): Guzzle
    {
        $path = realpath(__DIR__ . "/responses/$jsonFile");
        if ($path === false) {
            throw new RuntimeException("invalid jsonFile: $jsonFile");
        }
        $responseCode = (int) substr($jsonFile, 0 , 3);
        $handlerStack = HandlerStack::create(
            new MockHandler([
                new Response($responseCode, [], file_get_contents($path)),
            ])
        );
        return new Guzzle(['handler' => $handlerStack]);
    }

    /**
     * @param string $jsonFile
     * @param bool   $extendedClient
     *
     * @return ApiClientInterFace
     */
    public function getMockApiClient(string $jsonFile, bool $extendedClient = false): ApiClientInterFace
    {
        $config = self::getZsConfig();
        $guzzle = self::getGuzzleClient($jsonFile);
        return $extendedClient
            ? ClientFactory::createExtendedClient($config, $guzzle)
            : ClientFactory::createClient($config, $guzzle)
        ;
    }

    /**
     * Return Zend Server web API credentials. Replace this function with a custom solution or use vhost SetEnv to assign
     * params in a similar way.
     *
     * @return array
     */
    public static function getZsConfig(): array
    {
        return [
            'baseUrl' => 'https://your.zend.server.url',
            'hash' => 'fighters',
            'username' => 'foobar',
            'version' => 1.23,
        ];
    }
}