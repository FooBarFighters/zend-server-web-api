<?php


namespace FooBarFighters\ZendServer\WebApi\Model;

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
     * App constructor.
     */
    public function __construct(
        int $id
        , string $baseUrl
        , string $appName
        , ?string $deployedVersion
        , ?string $rollbackVersion
    )
    {
        $this->id = $id;
        $this->baseUrl = $baseUrl;
        $this->appName = $appName;
        $this->deployedVersion = $deployedVersion;
        $this->rollbackVersion = $rollbackVersion;
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
        );
    }
}