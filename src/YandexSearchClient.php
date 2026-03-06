<?php

namespace Tigusigalpa\YandexSearch;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Tigusigalpa\YandexCloudClient\YandexCloudClient;
use Tigusigalpa\YandexSearch\Resources\WebSearchResource;
use Tigusigalpa\YandexSearch\Resources\GenSearchResource;
use Tigusigalpa\YandexSearch\Resources\ImageSearchResource;
use Tigusigalpa\YandexSearch\Resources\WordstatResource;
use Tigusigalpa\YandexSearch\Resources\OperationResource;

class YandexSearchClient
{
    private const BASE_URL = 'https://searchapi.api.cloud.yandex.net';

    private YandexCloudClient $cloudClient;
    private string $folderId;
    private ClientInterface $httpClient;

    public function __construct(
        YandexCloudClient $cloudClient,
        string $folderId,
        ?ClientInterface $httpClient = null
    ) {
        $this->cloudClient = $cloudClient;
        $this->folderId = $folderId;
        $this->httpClient = $httpClient ?? new Client([
            'base_uri' => self::BASE_URL,
            'timeout' => 30,
            'http_errors' => false,
        ]);
    }

    public function web(): WebSearchResource
    {
        return new WebSearchResource($this->httpClient, $this->cloudClient, $this->folderId);
    }

    public function gen(): GenSearchResource
    {
        return new GenSearchResource($this->httpClient, $this->cloudClient, $this->folderId);
    }

    public function images(): ImageSearchResource
    {
        return new ImageSearchResource($this->httpClient, $this->cloudClient, $this->folderId);
    }

    public function wordstat(): WordstatResource
    {
        return new WordstatResource($this->httpClient, $this->cloudClient, $this->folderId);
    }

    public function operations(): OperationResource
    {
        return new OperationResource($this->httpClient, $this->cloudClient, $this->folderId);
    }

    public function getFolderId(): string
    {
        return $this->folderId;
    }

    public function getCloudClient(): YandexCloudClient
    {
        return $this->cloudClient;
    }

    public function getHttpClient(): ClientInterface
    {
        return $this->httpClient;
    }
}
