<?php


namespace FooBarFighters\ZendServer\WebApi\Model;

use DateTimeImmutable;

/**
 * Zend Server App model
 *
 * Class App
 * @package FooBarFighters\ZendServer\Model
 */
class App implements \JsonSerializable
{
    /**
     * @var string|null
     */
    private $appName;

    /**
     * @var string|null
     */
    private $baseUrl;

    /**
     * @var string|null
     */
    private $deployedVersion;

    /**
     * @var string|null
     */
    private $env;

    /**
     * @var int|null
     */
    private $id;

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
        ?int $id = null
        , ?string $appName = null
        , ?string $deployedVersion = null
        , ?string $rollbackVersion = null
        , ?string $timestamp = null
        , ?string $baseUrl = null
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
     * @return string
     */
    public function getDeployedVersion(): string
    {
        return $this->deployedVersion;
    }

    /**
     * @return string
     */
    public function getEnv(): ?string
    {
        return $this->env;
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
    public function getName(): string
    {
        return $this->appName;
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
     * @return string
     */
    public function getUrl(): string
    {
        return $this->baseUrl;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'version' => $this->getDeployedVersion(),
            'rollback' => $this->getRollbackVersion(),
            'updated' => $this->getTimestampAsString(),
            'url' => $this->getUrl(),
        ];
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
            , $data['appName']
            , $data['deployedVersions']['deployedVersion']
            , $data['deployedVersions']['applicationRollbackVersion'] ?? null
            , $data['creationTime']
            , $data['baseUrl']
        );
    }

    /**
     * @param string $env
     *
     * @return App
     */
    public function setEnv(string $env): self
    {
        $this->env = $env;
        return $this;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}