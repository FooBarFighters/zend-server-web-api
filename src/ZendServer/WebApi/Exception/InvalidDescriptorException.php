<?php


namespace FooBarFighters\ZendServer\WebApi\Exception;

use LibXMLError;

/**
 * The descriptor file is the deployment.xml residing in the package root.
 *
 * Class InvalidDescriptorException
 * @package FooBarFighters\Robo\Task\ZendServer\Exception
 */
class InvalidDescriptorException extends InvalidPackageException
{
    /**
     * @var LibXMLError[]
     */
    private $errors;

    /**
     * InvalidDescriptorException constructor.
     *
     * @param LibXMLError[] $errors
     */
    public function __construct(array $errors)
    {
        $this->errors = $errors;
        $error = $errors[0];
        parent::__construct($error->message, $error->code);
    }

    /**
     * @return LibXMLError[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}