<?php

namespace Tigusigalpa\YandexSearch\Resources;

use Tigusigalpa\YandexSearch\DTOs\WebSearch\OperationDTO;
use Tigusigalpa\YandexSearch\DTOs\WebSearch\SearchResultDTO;

class OperationResource extends BaseResource
{
    public function get(string $operationId): OperationDTO
    {
        $response = $this->request('GET', '/v2/operations/' . $operationId);

        $data = json_decode((string) $response->getBody(), true);

        $operation = OperationDTO::fromArray($data);

        if ($operation->done && isset($data['response']['rawData'])) {
            $xml = $this->parseXmlResponse($data['response']['rawData']);
            $searchResult = SearchResultDTO::fromXml($xml);
            
            return new OperationDTO(
                id: $operation->id,
                done: $operation->done,
                createdAt: $operation->createdAt,
                createdBy: $operation->createdBy,
                modifiedAt: $operation->modifiedAt,
                metadata: $operation->metadata,
                response: $searchResult,
                error: $operation->error
            );
        }

        return $operation;
    }
}
