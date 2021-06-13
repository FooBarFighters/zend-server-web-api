<?php declare(strict_types=1);

namespace FooBarFighters\ZendServer\WebApi\Client\Extended;

use FooBarFighters\ZendServer\WebApi\Client\ApiClientInterFace;
use FooBarFighters\ZendServer\WebApi\Client\Core\Client as CoreClient;
use FooBarFighters\ZendServer\WebApi\Client\Extended\Method\Deployment;

/**
 * Decorator for the core client that maps API responses to data models, which offer additional functionality like
 * filtering and sorting.
 *
 * Class Client
 * @package FooBarFighters\ZendServer\WebApi\Client\Extended
 */
final class Client implements ApiClientInterFace
{
    //== use traits to add decorator methods
    use Deployment;

    /**
     * @var Client
     */
    private $api;

    /**
     * Client constructor.
     *
     * @param CoreClient $api
     */
    public function __construct(CoreClient $api)
    {
        $this->api = $api;
    }

    /**
     * Proxy the core methods. Alternatively Client::getCoreClient() could be used.
     * NOTE: for IDE autocompletion the annotations in the extended traits should mirror the methods in the core traits.
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return false|mixed
     */
    public function __call(string $name, array $arguments)
    {
        if(method_exists($this->api, $name)){
            return call_user_func_array([$this->api, $name], $arguments);
        }
    }

    /**
     * @return CoreClient
     */
    public function getCoreClient(): CoreClient
    {
        return $this->api;
    }
}
