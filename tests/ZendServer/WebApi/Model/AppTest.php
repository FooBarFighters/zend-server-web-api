<?php

namespace Tests\ZendServer\WebApi\Model;

use FooBarFighters\ZendServer\WebApi\Client\Extended\Client;
use FooBarFighters\ZendServer\WebApi\Model\App;
use Tests\ZendServer\WebApi\TestCase;

final class AppTest extends TestCase
{
    /**
     * echo __FUNCTION__ . PHP_EOL;
     */
    public function testAppModelGetters(): void
    {
        echo __FUNCTION__ . PHP_EOL;

        /** @var Client $api */
        $api = $this->getMockApiClient('200.applicationGetStatus.json', true);

        //== grab the raw data from the first app in the result list
        $appData = $api->applicationGetStatus()['responseData']['applicationsList'][0];

        //== create app model
        $app = App::createFromApi($appData);

        //== test the shit out of it
        self::assertIsInt($app->getId());
        self::assertEquals($appData['id'], $app->getId());
        self::assertEquals($appData['appName'], $app->getName());
        self::assertEquals($appData['baseUrl'], $app->getUrl());
    }
}
