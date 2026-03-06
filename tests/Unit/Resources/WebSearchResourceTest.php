<?php

namespace Tigusigalpa\YandexSearch\Tests\Unit\Resources;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Tigusigalpa\YandexCloudClient\YandexCloudClient;
use Tigusigalpa\YandexSearch\Resources\WebSearchResource;
use Tigusigalpa\YandexSearch\DTOs\WebSearch\SearchResultDTO;
use Mockery;

class WebSearchResourceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testSearchReturnsSearchResultDTO(): void
    {
        $xmlResponse = '<?xml version="1.0" encoding="utf-8"?><yandexsearch version="1.0"><response><found>100</found><found-human>100</found-human><results><grouping><group><doc><url>https://example.com</url><domain>example.com</domain><title>Test Title</title></doc></group></grouping></results></response></yandexsearch>';
        
        $base64Xml = base64_encode($xmlResponse);
        
        $mockResponse = new Response(200, [], json_encode(['rawData' => $base64Xml]));
        
        $mock = new MockHandler([$mockResponse]);
        $handlerStack = HandlerStack::create($mock);
        $httpClient = new Client(['handler' => $handlerStack]);

        $cloudClient = Mockery::mock(YandexCloudClient::class);
        $cloudClient->shouldReceive('getIamToken')->andReturn('test-token');

        $resource = new WebSearchResource($httpClient, $cloudClient, 'test-folder-id');
        
        $result = $resource->search('test query');
        
        $this->assertInstanceOf(SearchResultDTO::class, $result);
        $this->assertEquals(100, $result->found);
    }
}
