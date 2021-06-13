<?php declare(strict_types=1);

namespace FooBarFighters\ZendServer\WebApi\Exception;

use RuntimeException;
use Throwable;

class ApiException extends RuntimeException
{
    /**
     * Full ZS API error response
     *
     * @var array
     */
    private $data;

    /**
     * ApiException constructor.
     */
    public function __construct(int $errorCode, array $data, Throwable $previous = null)
    {
        $this->data = $data;
        parent::__construct($data['errorData']['errorCode'], $errorCode, $previous);
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->data['errorData']['errorMessage'];
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}