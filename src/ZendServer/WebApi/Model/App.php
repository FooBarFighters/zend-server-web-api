<?php


namespace FooBarFighters\ZendServer\WebApi\Model;

use DateTimeImmutable;

/**
 * Zend Server App model
 *
 * Class App
 * @package FooBarFighters\ZendServer\Model
 */
class App
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var string
     */
    private $appName;

    /**
     * @var string|null
     */
    private $deployedVersion;

    /**
     * @var string|null
     */
    private $rollbackVersion;

    /**
     * @var DateTimeImmutable|false
     */
    private $timestamp;

    /**
     * App constructor.
     */
    public function __construct(
        int $id
        , string $baseUrl
        , string $appName
        , ?string $deployedVersion
        , ?string $rollbackVersion
        , ?string $timestamp
    )
    {
        $this->id = $id;
        $this->baseUrl = $baseUrl;
        $this->appName = $appName;
        $this->deployedVersion = $deployedVersion;
        $this->rollbackVersion = $rollbackVersion;
        $this->timestamp = DateTimeImmutable::createFromFormat('Y-m-d\TH:i:sP', $timestamp);
    }

    /**
     * App identifier
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->appName;
    }

    /**
     * @return string
     */
    public function getDeployedVersion(): string
    {
        return $this->deployedVersion;
    }

    /**
     * @return string|null
     */
    public function getRollbackVersion(): ?string
    {
        return $this->rollbackVersion;
    }

    /**
     * @return DateTimeImmutable|false
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return string
     */
    public function getTimestampAsString(string $format = 'Y-m-d H:i:s'): ?string
    {
        return $this->timestamp ? $this->timestamp->format($format) : null;
    }

    /**
     * @param array $data
     *
     * @return static
     */
    public static function createFromApi(array $data): self
    {
        return new self(
            (int)$data['id']
            , $data['baseUrl']
            , $data['appName']
            , $data['deployedVersions']['deployedVersion']
            , $data['deployedVersions']['applicationRollbackVersion'] ?? null
            , $data['creationTime']
        );
    }
}