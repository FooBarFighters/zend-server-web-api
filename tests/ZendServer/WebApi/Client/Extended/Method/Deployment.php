<?php

namespace Tests\ZendServer\WebApi\Client\Extended\Method;

use FooBarFighters\ZendServer\WebApi\Model\AppList;

trait Deployment
{
    final public function testGetApps(): void
    {
        echo __FUNCTION__ . PHP_EOL;

        $api = $this->getMockApiClient('200.getApplicationStatus.json', true);

        self::assertInstanceOf(AppList::class, $api->getApps());
    }
}