<?php

namespace Tigusigalpa\YandexSearch\Tests\Unit\Resources;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Tigusigalpa\YandexCloudClient\YandexCloudClient;
use Tigusigalpa\YandexSearch\Resources\WordstatResource;
use Tigusigalpa\YandexSearch\DTOs\Wordstat\WordstatTopDTO;
use Mockery;

class WordstatResourceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testGetTopReturnsWordstatTopDTO(): void
    {
        $mockResponse = new Response(200, [], json_encode([
            'phrase' => 'test phrase',
            'topPhrases' => [
                ['phrase' => 'related phrase 1', 'count' => 1000],
                ['phrase' => 'related phrase 2', 'count' => 500],
            ],
        ]));
        
        $mock = new MockHandler([$mockResponse]);
        $handlerStack = HandlerStack::create($mock);
        $httpClient = new Client(['handler' => $handlerStack]);

        $cloudClient = Mockery::mock(YandexCloudClient::class);
        $cloudClient->shouldReceive('getIamToken')->andReturn('test-token');

        $resource = new WordstatResource($httpClient, $cloudClient, 'test-folder-id');
        
        $result = $resource->getTop('test phrase');
        
        $this->assertInstanceOf(WordstatTopDTO::class, $result);
        $this->assertEquals('test phrase', $result->phrase);
    }
}
