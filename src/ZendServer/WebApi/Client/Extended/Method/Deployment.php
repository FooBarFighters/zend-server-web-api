<?php declare(strict_types=1);

namespace FooBarFighters\ZendServer\WebApi\Client\Extended\Method;

use FooBarFighters\ZendServer\WebApi\Model\App;
use FooBarFighters\ZendServer\WebApi\Model\AppList;
use RuntimeException;

/**
 *
 * @method applicationUpdate(int $appId, string $zipPath): array
 * @method applicationGetStatus(array $appIds = null, ?string $direction = null): array
 *
 * Trait Deployment
 * @package FooBarFighters\ZendServer\WebApi\Client\Extended\Method
 */
trait Deployment
{
    /**
     * Return the list of Apps
     *
     * @param array|null  $appIds
     * @param string|null $direction ASC|DESC
     *
     * @return AppList
     */
    public function getApps(array $appIds = null, ?string $direction = null): AppList
    {
        /** @var array[]|null $apps */
        $apps = $this->api->applicationGetStatus($appIds, $direction)['responseData']['applicationsList'] ?? [];

        return new AppList(array_map([App::class, 'createFromApi'], $apps));
    }

    /**
     * Update a Zend Server app.
     *
     * @param int    $appId
     * @param string $zipPath
     *
     * @return App|null
     */
    public function updateApp(int $appId, string $zipPath): ?App
    {
        //ddd($this->api->applicationUpdate($appId, $zipPath));
        $appData = $this->api->applicationUpdate($appId, $zipPath)['responseData']['applicationInfo'] ?? null;

        if($appData){
            //== return the updated App
            return App::createFromApi($appData);
        }

        //== or throw outofbounds exception?
        return null;
    }
}