<?php

namespace Tests\ZendServer\WebApi\Client\Core\Method;

use FooBarFighters\ZendServer\WebApi\Client\Core\Client;
use FooBarFighters\ZendServer\WebApi\Exception\NoSuchApplicationException;
use FooBarFighters\ZendServer\WebApi\Exception\NotAuthorizedException;
use GuzzleHttp\Client as Guzzle;
use UnexpectedValueException;

trait Deployment
{
    /**
     * Test if an invalid API credentials will trigger the proper exception.
     */
    final public function testAuthenticationException(): void
    {
        echo __FUNCTION__ . PHP_EOL;
        $this->expectException(NotAuthorizedException::class);
        $api = $this->getMockApiClient('401.authenticationError.json');

        //== trigger the exception
        $api->applicationGetStatus();
    }

    /**
     *  Test if an invalid $baseUrl will trigger the proper exception.
     */
    final public function testApiClientConstructionError(): void
    {
        echo __FUNCTION__ . PHP_EOL;
        $this->expectException(UnexpectedValueException::class);
        new Client('invalid_url','','',1.15, new Guzzle());
    }

    /**
     * Test an emulated response with 5 results.
     */
    final public function testApplicationGetStatus(): void
    {
        echo __FUNCTION__ . PHP_EOL;

        $api = $this->getMockApiClient('200.applicationGetStatus.json');
        $data = $api->applicationGetStatus();

        //== mock response should have an applicationsList with 5 entries
        self::assertISArray($data);
        self::assertNotNull($data['responseData']['applicationsList'] ?? null);
        self::assertCount(5, $data['responseData']['applicationsList']);
    }

    /**
     * Test applicationUpdate with a non existing app Id, to see if it will trigger the proper exception.
     */
    final public function testNoSuchApplication(): void
    {
        echo __FUNCTION__ . PHP_EOL;

        $this->expectException(NoSuchApplicationException::class);
        $api = $this->getMockApiClient('404.noSuchApplication.json');

        //== doesn't need to be an actual zip file, any existing file will do for testing purposes
        $zipPath = "$this->responseDir/404.noSuchApplication.json";

        //== trigger the exception
        $api->applicationUpdate(123456789, $zipPath);
    }
}