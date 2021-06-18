<?php

namespace FooBarFighters\ZendServer\WebApi\Repository;

use FooBarFighters\ZendServer\WebApi\Model\App;

/**
 * Class AppList
 *
 * Repository for App instances offering the possibility to reduce the selection based on a range of filtering criteria.
 * NOTE: Filtering returns a new instance of AppList containing the result. It doesn't modify the current instance. This
 * allows for branching off a range of sub-results from the same parent.
 *
 * @package FooBarFighters\ZendServer\Model
 */
class AppList extends \ArrayObject
{
    /**
     * Filter the AppList based on a callback function, for example:
     *
     *  $id = 70;
     *  $appList = $appList->filter(function(\FooBarFighters\ZendServer\Model\App $app) use($id) {
     *    return $app->getId() === $id;
     *  });
     *
     * NOTE: The returned AppList is a new instance, it does not modify the current one.
     *
     * @param callable $fn
     *
     * @return AppList
     */
    public function filter(callable $fn): AppList
    {
        return new AppList(array_filter($this->getArrayCopy(), $fn));
    }

    /**
     * Return app(s) that match the identifier(s).
     *
     * @param int|int[] $id
     *
     * @return App|AppList|null
     */
    public function filterById($id)
    {
        if (is_int($id)) {
            /** @var App $app */
            foreach ($this->getArrayCopy() as $app) {
                if ($app->getId() === $id) {
                    return $app;
                }
            }
        }
        if (is_array($id)) {
            return new AppList(
                array_filter($this->getArrayCopy(), static function (App $app) use ($id) {
                    return in_array($app->getId(), $id, true);
                })
            );
        }
        return null;
    }

    /**
     * Return an app with a specific name.
     *
     * @param string $name
     *
     * @return App|null
     */
    public function filterByName(string $name): ?App
    {
        if (empty($name)) {
            return null;
        }
        /** @var App $app */
        foreach ($this->getArrayCopy() as $app) {
            if ($app->getName() === $name) {
                return $app;
            }
        }
        return null;
    }

    /**
     * Return a list of apps matched on the given pattern either by name or url
     *
     * @param string $regex i.e.: '/^FooBarFighters[O|T]*\d*$/'
     * @param string $property
     *
     * @return AppList
     */
    public function filterByPattern(string $regex, string $property = 'name'): AppList
    {
        $this->validate($property);
        return new AppList(
            array_filter($this->getArrayCopy(), static function (App $app) use ($regex, $property) {
                return preg_match($regex, $property === 'name' ? $app->getName() : $app->getUrl());
            })
        );
    }

    /**
     * TODO add sort alphabetically for url
     * NOTE: sorting by name can be done with the API applicationGetStatus $direction param
     */
    public function sort(string $property = 'id'): AppList
    {
        $this->validate($property, ['id', 'name', 'url']);
        $apps = $this->getArrayCopy();
        usort($apps, static function ($a, $b) {
            return $a->getId() < $b->getId() ? 1 : -1;
        });
        return new AppList($apps);
    }

    /**
     * Get app by numerical index instead of by Id
     *
     * @param int $index
     *
     * @return App|null
     */
    public function getAppByIndex(int $index): ?App
    {
        $apps = $this->getArrayCopy();
        $id = array_keys($apps)[$index] ?? null;
        return $apps[$id] ?? null;
    }

    /**
     * Alias of toArray
     *
     * @return array[]
     */
    public function getNames(): array
    {
        return $this->toArray();
    }

    /**
     * Return an array[] of [appId => appName]
     *
     * @return array[]
     */
    public function toArray(): array
    {
        $appList = [];
        /** @var App $app */
        foreach ($this->getArrayCopy() as $app) {
            $appList[$app->getId()] = $app->getName();
        }
        return $appList;
    }

    /**
     * Validate allowed values.
     *
     * @param string         $property
     * @param array|string[] $allowedValues
     */
    private function validate(string $property, array $allowedValues = ['name', 'url']): void
    {
        if (!in_array($property, $allowedValues, true)) {
            throw new \RuntimeException('invalid property name [' . implode('|', $allowedValues) . "]: $property");
        }
    }
}