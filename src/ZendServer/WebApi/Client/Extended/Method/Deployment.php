<?php declare(strict_types=1);

namespace FooBarFighters\ZendServer\WebApi\Client\Extended\Method;

use FooBarFighters\ZendServer\WebApi\Model\App;
use FooBarFighters\ZendServer\WebApi\Repository\AppList;

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
     * Return the list of Apps.
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
     * Rollback an existing application to its previous version.
     *
     * @param int $appId
     *
     * @return App|null
     */
    public function rollbackApp(int $appId): ?App
    {
        return self::createApp($this->api->applicationRollback($appId));
    }

    /**
     * Update a Zend Server app.
     *
     * @param int        $appId
     * @param string     $zipPath
     * @param bool|null  $ignoreFailures
     * @param array|null $userParams
     *
     * @return App|null
     */
    public function updateApp(int $appId, string $zipPath, ?bool $ignoreFailures = null, ?array $userParams = null): ?App
    {
        $data = $this->api->applicationUpdate($appId, $zipPath, $ignoreFailures, $userParams);
        return self::createApp($data);
    }

    /**
     * Create an app model from api data
     *
     * @param array $data
     *
     * @return App|null
     */
    public static function createApp(array $data): ?App
    {
        $appData = $data['responseData']['applicationInfo'] ?? null;
        return empty($appData) ? null : App::createFromApi($appData);
    }
}