<?php

namespace Tigusigalpa\YandexSearch\Exceptions;

class ApiException extends YandexSearchException
{
    protected int $statusCode;
    protected string $responseBody;

    public function __construct(string $message, int $statusCode, string $responseBody = '', ?\Throwable $previous = null)
    {
        $this->statusCode = $statusCode;
        $this->responseBody = $responseBody;

        $fullMessage = sprintf(
            'API Error [%d]: %s',
            $statusCode,
            $message
        );

        if (!empty($responseBody)) {
            $fullMessage .= sprintf(' | Response: %s', $responseBody);
        }

        parent::__construct($fullMessage, $statusCode, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getResponseBody(): string
    {
        return $this->responseBody;
    }
}
