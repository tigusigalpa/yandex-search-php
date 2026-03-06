<?php

namespace Tigusigalpa\YandexSearch\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tigusigalpa\YandexCloudClient\YandexCloudClient;
use Tigusigalpa\YandexSearch\YandexSearchClient;
use Tigusigalpa\YandexSearch\Resources\WebSearchResource;
use Tigusigalpa\YandexSearch\Resources\GenSearchResource;
use Tigusigalpa\YandexSearch\Resources\ImageSearchResource;
use Tigusigalpa\YandexSearch\Resources\WordstatResource;
use Tigusigalpa\YandexSearch\Resources\OperationResource;
use Mockery;

class YandexSearchClientTest extends TestCase
{
    private YandexSearchClient $client;
    private YandexCloudClient $cloudClient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cloudClient = Mockery::mock(YandexCloudClient::class);
        $this->client = new YandexSearchClient($this->cloudClient, 'test-folder-id');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testClientInstantiation(): void
    {
        $this->assertInstanceOf(YandexSearchClient::class, $this->client);
        $this->assertEquals('test-folder-id', $this->client->getFolderId());
    }

    public function testWebResourceGetter(): void
    {
        $resource = $this->client->web();
        $this->assertInstanceOf(WebSearchResource::class, $resource);
    }

    public function testGenResourceGetter(): void
    {
        $resource = $this->client->gen();
        $this->assertInstanceOf(GenSearchResource::class, $resource);
    }

    public function testImagesResourceGetter(): void
    {
        $resource = $this->client->images();
        $this->assertInstanceOf(ImageSearchResource::class, $resource);
    }

    public function testWordstatResourceGetter(): void
    {
        $resource = $this->client->wordstat();
        $this->assertInstanceOf(WordstatResource::class, $resource);
    }

    public function testOperationsResourceGetter(): void
    {
        $resource = $this->client->operations();
        $this->assertInstanceOf(OperationResource::class, $resource);
    }
}
