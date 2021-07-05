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
     * @var App
     */
    private $app;

    /**
     * @var string|null
     */
    private $fileName;

    /**
     * @var string
     */
    private $path;

    /**
     * Package constructor.
     */
    public function __construct(App $app, string $path)
    {
        $this->app = $app;
        $this->path = $path;
    }

    /**
     * Get the app definition in the package.
     *
     * @return App
     */
    public function getApp(): App
    {
        return $this->app;
    }

    /**
     * Get the name of the app in the package.
     *
     * @return string
     */
    public function getAppName(): string
    {
        return $this->app->getName();
    }

    /**
     * Get the name of the package file.
     *
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
     * Get the (local) path of the package file.
     *
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->path;
    }

    /**
     * Get the size of the package file.
     *
     * @param string $format
     *
     * @return false|float|int
     */
    public function getFileSize(string $format = 'kb')
    {
        $numBytes = filesize($this->path);
        switch ($format){
            case 'bytes':
                return $numBytes;
            case 'kb':
                return $numBytes/1024;
            case 'mb':
                return $numBytes/(1024 * 1024);
        }
        return $numBytes;
    }

    /**
     * Get the release version of the app.
     *
     * @return string
     */
    public function getReleaseVersion(): string
    {
        return $this->app->getDeployedVersion();
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
            new App(null, (string)$descriptor->name, (string)$descriptor->version->release)
            , $zipPath
        );
    }
}
