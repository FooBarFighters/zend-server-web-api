<?php

namespace FooBarFighters\ZendServer\WebApi\Client\Core\Method;

use GuzzleHttp\RequestOptions;
use ReflectionClass;
use UnexpectedValueException;

trait ServerClusterManagement
{
    /**
     * Read a certain number of log lines from the end of the file log. If serverId is passed, then the request will be
     * performed against that cluster member, otherwise it is performed locally.
     *
     * @param string      $logName     See LogName::class constants
     * @param int|null    $serverId    If passed, the log contents will be fetched from that cluster member, otherwise performed locally.
     * @param int|null    $linesToRead How many lines to read
     * @param string|null $filter      Apply a certain case-insensitive string filter to the log lines. , i.e. 'Notice|Deprecated'
     *
     * @return array
     */
    public function logsReadLines(string $logName, ?int $serverId = null, ?int $linesToRead = null, ?string $filter = null): array
    {
        $reflection = new ReflectionClass(LogName::class);
        $allowedValues = $reflection->getConstants();
        if(!in_array($logName, $allowedValues, true)){
            throw new UnexpectedValueException("invalid logName: $logName");
        }

        return $this->request('GET',  '/ZendServer/Api/logsReadLines', [
            //== add not null params
            RequestOptions::QUERY => array_filter(get_defined_vars(), static function($v){return $v !== null;})
        ]);
    }
}