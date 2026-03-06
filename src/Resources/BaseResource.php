<?php

namespace Tigusigalpa\YandexSearch\Resources;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Tigusigalpa\YandexCloudClient\YandexCloudClient;
use Tigusigalpa\YandexSearch\Exceptions\ApiException;
use Tigusigalpa\YandexSearch\Exceptions\YandexSearchException;

abstract class BaseResource
{
    protected ClientInterface $httpClient;
    protected YandexCloudClient $cloudClient;
    protected string $folderId;

    public function __construct(
        ClientInterface $httpClient,
        YandexCloudClient $cloudClient,
        string $folderId
    ) {
        $this->httpClient = $httpClient;
        $this->cloudClient = $cloudClient;
        $this->folderId = $folderId;
    }

    protected function getIamToken(): string
    {
        return $this->cloudClient->getIamToken();
    }

    protected function request(string $method, string $uri, array $options = []): ResponseInterface
    {
        $iamToken = $this->getIamToken();

        $options['headers'] = array_merge($options['headers'] ?? [], [
            'Authorization' => 'Bearer ' . $iamToken,
            'Content-Type' => 'application/json',
        ]);

        try {
            $response = $this->httpClient->request($method, $uri, $options);

            if ($response->getStatusCode() >= 400) {
                $this->handleErrorResponse($response);
            }

            return $response;
        } catch (GuzzleException $e) {
            throw new YandexSearchException(
                'HTTP request failed: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    protected function handleErrorResponse(ResponseInterface $response): void
    {
        $statusCode = $response->getStatusCode();
        $body = (string) $response->getBody();

        $errorMessage = 'Request failed';

        $decoded = json_decode($body, true);
        if (json_last_error() === JSON_ERROR_NONE && isset($decoded['message'])) {
            $errorMessage = $decoded['message'];
        } elseif (json_last_error() === JSON_ERROR_NONE && isset($decoded['error'])) {
            $errorMessage = is_string($decoded['error']) ? $decoded['error'] : json_encode($decoded['error']);
        }

        throw new ApiException($errorMessage, $statusCode, $body);
    }

    protected function parseXmlResponse(string $base64Data): \SimpleXMLElement
    {
        $xmlData = base64_decode($base64Data);

        if ($xmlData === false) {
            throw new YandexSearchException('Failed to decode base64 XML data');
        }

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xmlData);

        if ($xml === false) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            $errorMessages = array_map(fn($error) => $error->message, $errors);
            throw new YandexSearchException('Failed to parse XML: ' . implode(', ', $errorMessages));
        }

        return $xml;
    }
}
