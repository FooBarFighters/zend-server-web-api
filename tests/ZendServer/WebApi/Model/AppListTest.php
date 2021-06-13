<?php

namespace Tests\ZendServer\WebApi\Model;

use FooBarFighters\ZendServer\WebApi\Model\App;
use Tests\ZendServer\WebApi\TestCase;

final class AppListTest extends TestCase
{

    /**
     * Test the getter functions of the AppList model
     */
    public function testAppListModelGetters(): void
    {
        echo __FUNCTION__ . PHP_EOL;

        //== get Zend Server facade with a mock client
        $zs = $this->getMockApiClient('200.applicationGetStatus.json', true);

        //== full list of apps
        $apps = $zs->getApps();

        //== test filter method
        $app = $apps->filter(
            static function(App $app){
                return $app->getName() === 'FooBarFighters';
            }
        )->getAppByIndex(0);
        self::assertEquals('FooBarFighters', $app->getName());

        //== test filterByPattern method
        $filteredApps = $apps->filterByPattern('/^FooU[1|2]*\d*$/');
        self::assertCount(2, $filteredApps);

        //== test toArray method
        self::assertIsArray($filteredApps->toArray());

        //== test filterByName method
        self::assertEquals(70, $apps->filterByName('FooU1')->getId());

        //== test filterById method
        self::assertEquals('FooU1', $apps->filterById(70)->getName());
    }
}
