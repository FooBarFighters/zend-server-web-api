<?php declare(strict_types=1);

namespace FooBarFighters\ZendServer\WebApi\Client\Core\Method;

use FooBarFighters\ZendServer\WebApi\Exception\ApplicationConflictException;
use FooBarFighters\ZendServer\WebApi\Exception\InvalidParameterException;
use FooBarFighters\ZendServer\WebApi\Exception\MissingParameterException;
use FooBarFighters\ZendServer\WebApi\Exception\NoRollbackAvailableException;
use FooBarFighters\ZendServer\WebApi\Exception\NoSuchApplicationException;
use GuzzleHttp\RequestOptions;
use RuntimeException;

/**
 * Trait Deployment
 *
 * @url https://help.zend.com/zend/current/content/deployment_methods.htm
 * @package FooBarFighters\ZendServer\Api
 */
trait Deployment
{
    /**
     * Get the list of applications currently deployed (or staged) on the server or the cluster and information about
     * each application. If application IDs are specified, this method will return information about the specified
     * applications. If no IDs are specified, this method will return information about all applications
     * on the server or cluster.
     *
     * @url https://help.zend.com/zend/current/content/the_applicationgetstatus_method.htm
     * @version 1.2
     *
     * @param array|null  $appIds
     * @param string|null $direction ASC|DESC
     *
     * @return array
     */
    public function applicationGetStatus(array $appIds = null, ?string $direction = null): array
    {
        $options = [];
        if(!empty($appIds)){
            $options[RequestOptions::QUERY]['applications'] = $appIds;
        }
        if($direction && in_array($direction, ['ASC', 'DESC'])) {
            $options[RequestOptions::QUERY]['direction'] = $direction;
        }
        return $this->request('GET',  '/ZendServer/Api/applicationGetStatus', $options);
    }

    /**
     * Rollback an existing application to its previous version. This process is asynchronous, meaning the initial
     * request will start the rollback process and the initial response will show information about the application
     * being rolled back. You must continue checking the application status using the applicationGetStatus method until
     * the process is complete.
     *
     * @link https://help.zend.com/zend/current/content/the_applicationrollback_method.htm
     *
     * @param int $appId
     *
     * @throws NoRollbackAvailableException
     * @throws NoSuchApplicationException
     *
     * @return array
     */
    public function applicationRollback(int $appId): array
    {
        return $this->request('POST',  '/ZendServer/Api/applicationRollback', [
            //== application/x-www-form-urlencoded
            RequestOptions::FORM_PARAMS => ['appId' => $appId]
        ]);
    }

    /**
     * This method allows you to update an existing application. The package you provide must contain the same
     * application. Additionally, any new parameters or new values for existing parameters must be provided.
     * This process is asynchronous, meaning the initial request will wait until the package is uploaded and verified,
     * and the initial response will show information about the new version being deployed.
     * However, the staging and activation process will proceed after the response is returned.
     * You must continue checking the application status using the applicationGetStatus method until the deployment
     * process is complete.
     *
     * @url https://help.zend.com/zend/current/content/the_applicationupdate_method.htm
     * @version 1.2
     *
     * @param int        $appId      Zend Server application id
     * @param string     $appPackage path to the zip/zpk file that needs to be uploaded
     * @param bool|null  $ignoreFailures
     * @param array|null $userParams
     *
     * @throws ApplicationConflictException
     * @throws InvalidParameterException
     * @throws MissingParameterException
     * @throws NoSuchApplicationException
     *
     * @return array
     */
    public function applicationUpdate(int $appId, string $appPackage, ?bool $ignoreFailures = null, ?array $userParams = null): array
    {
        if(($path = realpath($appPackage)) === false){
            throw new RuntimeException("path doesn't exist: $appPackage");
        }
        $parts = explode('/', $appPackage);
        $fileName = end($parts);

        $requestOptions = [
            RequestOptions::MULTIPART => [
                [
                    'name'     => 'appId',
                    'contents' => $appId,
                ],
                [
                    'name'     => 'appPackage',
                    'contents' => fopen($appPackage, 'rb'),
                    'filename' => $fileName,
                    'Content-type' => 'application/vnd.zend.applicationpackage'
                ],
            ],
        ];

        if($ignoreFailures !== null){
            $requestOptions[RequestOptions::MULTIPART][] = [
                'name'     => 'ignoreFailures',
                'contents' => $ignoreFailures ? 'TRUE' : 'FALSE'
            ];
        }

        if(!empty($userParams)){
            foreach($userParams as $k => $v){
                $requestOptions[RequestOptions::MULTIPART][] = [
                    'name'     => "userParams%5B{$k}%5D",
                    'contents' => $v
                ];
            }
        }

        return $this->request('POST', '/ZendServer/Api/applicationUpdate', $requestOptions);
    }
}