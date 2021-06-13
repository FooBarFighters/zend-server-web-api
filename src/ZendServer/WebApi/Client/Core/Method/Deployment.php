<?php declare(strict_types=1);

namespace FooBarFighters\ZendServer\WebApi\Client\Core\Method;

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
     * @param int    $appId ZendServer application id
     * @param string $zipPath
     *
     * @throws RuntimeException
     * @return array
     */
    public function applicationUpdate(int $appId, string $zipPath): array
    {
        if(($path = realpath($zipPath)) === false){
            throw new RuntimeException("path doesn't exist: $zipPath");
        }
        $parts = explode('/', $zipPath);
        $fileName = end($parts);

        return $this->request('POST', '/ZendServer/Api/applicationUpdate', [
            RequestOptions::MULTIPART => [
                [
                    'name'     => 'appId',
                    'contents' => $appId,
                ],
                [
                    'name'     => 'appPackage',
                    'contents' => fopen($zipPath, 'rb'),
                    'filename' => $fileName,
                    'Content-type' => 'application/vnd.zend.applicationpackage'
                ],
            ],
        ]);
    }

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
}