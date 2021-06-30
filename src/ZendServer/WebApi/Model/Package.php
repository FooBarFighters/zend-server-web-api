<?php

namespace FooBarFighters\ZendServer\WebApi\Model;

use DOMDocument;
use FooBarFighters\ZendServer\WebApi\Exception\InvalidArchiveException;
use FooBarFighters\ZendServer\WebApi\Exception\InvalidDescriptorException;
use FooBarFighters\ZendServer\WebApi\Exception\InvalidPackageException;
use SimpleXMLElement;

class Package
{
    /**
     * @var string
     */
    private $fileName;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $version;

    /**
     * @var string
     */
    private $path;

    /**
     * Package constructor.
     */
    public function __construct(string $name, string $version, string $path)
    {
        $this->name = $name;
        $this->version = $version;
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getAppName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        if($this->fileName === null){
            $parts = explode(DIRECTORY_SEPARATOR, realpath($this->path));
            $this->fileName = end($parts);
        }
        return $this->fileName;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Parse and validate the package archive
     *
     * @param string $zipPath
     *
     * @return SimpleXMLElement
     */
    public static function getDescriptor(string $zipPath): SimpleXMLElement
    {
        $zip = new \ZipArchive();
        if($zip->open($zipPath) === false){
            throw new InvalidArchiveException("$zipPath is not a valid zip archive");
        }
        if(($xml = $zip->getFromName('deployment.xml')) === false){
            throw new InvalidPackageException('deployment.xml could not be found in the archive root');
        }
        return self::validateDescriptorFile($xml);
    }

    /**
     * @param string $xml
     *
     * @return SimpleXMLElement
     */
    public static function validateDescriptorFile(string $xml): SimpleXMLElement
    {
        libxml_use_internal_errors(true);
        $validator = dirname(__DIR__, 4) . '/resources/package/validation/deployment.xsd';
        $domDocument= new DOMDocument();
        $domDocument->loadXML($xml);
        if (!$domDocument->schemaValidate($validator)) {
            throw new InvalidDescriptorException(libxml_get_errors());
        }
        return simplexml_import_dom($domDocument);
    }

    /**
     * @param string $zipPath
     *
     * @return Package
     */
    public static function createFromArchive(string $zipPath): self
    {
        $descriptor = self::getDescriptor($zipPath);

        return new self(
            (string)$descriptor->name,
            (string)$descriptor->version->release,
            $zipPath
        );
    }
}
