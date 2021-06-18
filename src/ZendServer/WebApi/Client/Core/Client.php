<?php declare(strict_types=1);

namespace FooBarFighters\ZendServer\WebApi\Client\Core;

use FooBarFighters\ZendServer\WebApi\Client\ApiClientInterFace;
use FooBarFighters\ZendServer\WebApi\Exception\ApiException;
use FooBarFighters\ZendServer\WebApi\Client\Core\Method\Deployment;
use FooBarFighters\ZendServer\WebApi\Client\Core\Method\Monitor;
use FooBarFighters\ZendServer\WebApi\Client\Core\Method\ServerClusterManagement;
use FooBarFighters\ZendServer\WebApi\Exception\NotAuthorizedException;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use UnexpectedValueException;
use JsonException;

/**
 *
 * Zend Server Web API. Handles:
 * - signing requests (header related parameters)
 * - returning json responses parsed into an array
 * - throwing a custom exceptions if the api response indicates there was an error
 *
 * @url https://help.zend.com/zend/current/content/web_api_methods.htm
 *
 * Class Client
 * @package FooBarFighters\ZendServer\Api
 */
final class Client implements ApiClientInterFace
{
    //== use traits to add API methods
    use Deployment;
    use Monitor;
    use ServerClusterManagement;

    /**
     * ZendServer admin > Administration > Web API Keys > Hash
     *
     * @var string
     */
    private $apiHash;

    /**
     * ZendServer admin > Administration > Web API Keys > UserName
     *
     * @var string
     */
    private $apiUserName;

    /**
     * See Versions table which lists the various versions of the Zend Server Web API and their corresponding product
     * version on https://help.zend.com/zend/current/content/web_api_reference_guide.htm
     *
     * @var string
     */
    private $apiVersion;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * Curl Wrapper: https://github.com/guzzle/guzzle
     *
     * @var Guzzle
     */
    private $guzzle;

    /**
     * Hostname of Zend Server, i.e: my.zendserver.local
     *
     * @var string
     */
    private $host;

    /**
     * Value used in header request signature
     *
     * @var string
     */
    private $userAgent;

    /**
     *
     * @param string      $baseUrl
     * @param string      $apiHash
     * @param string      $apiUserName
     * @param string      $apiVersion
     * @param Guzzle|null $guzzle
     * @param string      $userAgent
     */
    public function __construct(
        string $baseUrl
        , string $apiHash
        , string $apiUserName
        , string $apiVersion
        , ?Guzzle $guzzle = null
        , string $userAgent = 'Zend_Http_Client/1.10'
    )
    {
        if (preg_match('/^(?<protocol>https?):\/\/(?<host>[^\/]*)/', $baseUrl, $m)) {
            //== extract the host from the base url
            $this->host = $m['host'];
            //== make sure the base url doesn't end with a black slash
            $this->baseUrl = "{$m['protocol']}://{$m['host']}";
        } else {
            throw new UnexpectedValueException("invalid baseUrl: $baseUrl");
        }

        $this->apiHash = $apiHash;
        $this->apiUserName = $apiUserName;
        $this->apiVersion = $apiVersion;
        $this->guzzle = $guzzle ?? new Guzzle();
        $this->userAgent = $userAgent;
    }

    /**
     * Send Zend Server API request and return the result as an array.
     *
     * Note: when posting files, Guzzle will use the header 'Content-Type: multipart/form-data' and calculate the
     * Content-Length for each post item
     *
     * @param string $method
     * @param string $endpoint
     * @param array  $options
     *
     * @throws GuzzleException|JsonException
     * @return array
     */
    private function request(string $method, string $endpoint, array $options = []): array
    {
        $ts = gmdate('D, d M Y H:i:s') . ' GMT';

        //== signature required for API access
        $signature = $this->getRequestSignature($endpoint, $ts);

        //== request options
        $options = array_merge(
            $options,
            [
                RequestOptions::VERIFY => false,
                RequestOptions::HEADERS => [
                    'Accept' => 'application/vnd.zend.serverapi+json;version=' . $this->apiVersion,
                    'Date' => $ts,
                    'User-Agent' => $this->userAgent,
                    'X-Zend-Signature' => "{$this->apiUserName}; $signature",
                ]
            ]
        );

        try {
            $response = $this->guzzle->request($method, "{$this->baseUrl}{$endpoint}", $options);
            $data = $this->parseResponse($response);

            //== fallback, most (if not all) exceptions should be thrown by Guzzle initially, based on HTTP status codes
            if (isset($data['errorData'])) {
                throw new ApiException(500, $data);
            }
            return $data;
        }

        //== re-package API related errors, this includes 500 and 4xx errors
        catch (BadResponseException $e) {
            $data = $this->parseResponse($e->getResponse());
            $errorCode = $data['errorData']['errorCode'] ?? null;

            if($errorCode === null){
                throw new ApiException($e->getCode(), $data);
            }

            switch($errorCode){
                case 'authError':
                    throw new NotAuthorizedException($e->getCode(), $data);
                    break;

                //== use errorCode to dynamically throw a custom Exception
                default:
                    $className = ucfirst($data['errorData']['errorCode']);
                    $qualifiedName = "\FooBarFighters\ZendServer\WebApi\Exception\\{$className}Exception";
                    if(class_exists($qualifiedName)){
                        throw new $qualifiedName($e->getCode(), $data);
                    }

            }

            //== custom error class was not found, throw the default API exception
            throw new ApiException($e->getCode(), $data);
        }
    }

    /**
     * Return a signature to sign APi requests to Zend Server.
     *
     * @url https://help.zend.com/zend/current/content/signing_api_requests.htm
     *
     * @param string $endpoint
     * @param string $ts
     *
     * @return string
     */
    private function getRequestSignature(string $endpoint, string $ts): string
    {
        return hash_hmac(
            'sha256'
            , "{$this->host}:{$endpoint}:{$this->userAgent}:{$ts}"
            , $this->apiHash
        );
    }

    /**
     * Convert a response object to an array
     *
     * @param ResponseInterface $response
     *
     * @throws JsonException
     * @return array
     */
    private function parseResponse(ResponseInterface $response): array
    {
        $response->getBody()->rewind();
        return json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
    }
}