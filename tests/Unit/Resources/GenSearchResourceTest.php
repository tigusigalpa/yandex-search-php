<?php

namespace Tigusigalpa\YandexSearch\Tests\Unit\Resources;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Tigusigalpa\YandexCloudClient\YandexCloudClient;
use Tigusigalpa\YandexSearch\Resources\GenSearchResource;
use Tigusigalpa\YandexSearch\DTOs\GenSearch\GenSearchResponseDTO;
use Mockery;

class GenSearchResourceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testSearchReturnsGenSearchResponseDTO(): void
    {
        $mockResponse = new Response(200, [], json_encode([
            'answer' => 'Test answer',
            'sources' => [],
            'searchQueries' => ['test query'],
            'isAnswerRejected' => false,
            'isBulletAnswer' => false,
        ]));
        
        $mock = new MockHandler([$mockResponse]);
        $handlerStack = HandlerStack::create($mock);
        $httpClient = new Client(['handler' => $handlerStack]);

        $cloudClient = Mockery::mock(YandexCloudClient::class);
        $cloudClient->shouldReceive('getIamToken')->andReturn('test-token');

        $resource = new GenSearchResource($httpClient, $cloudClient, 'test-folder-id');
        
        $result = $resource->search([
            ['role' => 'ROLE_USER', 'content' => 'What is Laravel?']
        ]);
        
        $this->assertInstanceOf(GenSearchResponseDTO::class, $result);
        $this->assertEquals('Test answer', $result->answer);
    }
}
