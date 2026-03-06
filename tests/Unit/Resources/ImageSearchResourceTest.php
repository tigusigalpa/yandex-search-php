<?php

namespace Tigusigalpa\YandexSearch\Tests\Unit\Resources;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Tigusigalpa\YandexCloudClient\YandexCloudClient;
use Tigusigalpa\YandexSearch\Resources\ImageSearchResource;
use Tigusigalpa\YandexSearch\DTOs\ImageSearch\ImageSearchResponseDTO;
use Mockery;

class ImageSearchResourceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testSearchReturnsImageSearchResponseDTO(): void
    {
        $mockResponse = new Response(200, [], json_encode([
            'images' => [
                [
                    'url' => 'https://example.com/image.jpg',
                    'format' => 'jpeg',
                    'width' => 800,
                    'height' => 600,
                ]
            ],
            'total' => 1,
        ]));
        
        $mock = new MockHandler([$mockResponse]);
        $handlerStack = HandlerStack::create($mock);
        $httpClient = new Client(['handler' => $handlerStack]);

        $cloudClient = Mockery::mock(YandexCloudClient::class);
        $cloudClient->shouldReceive('getIamToken')->andReturn('test-token');

        $resource = new ImageSearchResource($httpClient, $cloudClient, 'test-folder-id');
        
        $result = $resource->searchByImage(['url' => 'https://example.com/search.jpg']);
        
        $this->assertInstanceOf(ImageSearchResponseDTO::class, $result);
        $this->assertCount(1, $result->images);
    }
}
