<?php

namespace Tigusigalpa\YandexSearch\Resources;

use Tigusigalpa\YandexSearch\DTOs\WebSearch\SearchResultDTO;
use Tigusigalpa\YandexSearch\DTOs\WebSearch\OperationDTO;

class WebSearchResource extends BaseResource
{
    public function search(string $query, array $params = []): SearchResultDTO
    {
        $body = $this->buildSearchRequestBody($query, $params);

        $response = $this->request('POST', '/v2/web/search', [
            'json' => $body,
        ]);

        $data = json_decode((string) $response->getBody(), true);

        if (!isset($data['rawData'])) {
            throw new \Tigusigalpa\YandexSearch\Exceptions\YandexSearchException(
                'Invalid response: missing rawData field'
            );
        }

        $xml = $this->parseXmlResponse($data['rawData']);

        return SearchResultDTO::fromXml($xml);
    }

    public function searchAsync(string $query, array $params = []): OperationDTO
    {
        $body = $this->buildSearchRequestBody($query, $params);

        $response = $this->request('POST', '/v2/web/search_async', [
            'json' => $body,
        ]);

        $data = json_decode((string) $response->getBody(), true);

        return OperationDTO::fromArray($data);
    }

    private function buildSearchRequestBody(string $query, array $params): array
    {
        $body = [
            'folderId' => $this->folderId,
            'query' => [
                'searchType' => $params['searchType'] ?? 'SEARCH_TYPE_RU',
                'queryText' => $query,
                'familyMode' => $params['familyMode'] ?? 'FAMILY_MODE_MODERATE',
                'page' => $params['page'] ?? 0,
                'fixTypoMode' => $params['fixTypoMode'] ?? 'FIX_TYPO_MODE_ON',
            ],
        ];

        if (isset($params['sortSpec'])) {
            $body['sortSpec'] = [
                'sortMode' => $params['sortSpec']['sortMode'] ?? 'SORT_MODE_BY_RELEVANCE',
                'sortOrder' => $params['sortSpec']['sortOrder'] ?? 'SORT_ORDER_DESC',
            ];
        }

        if (isset($params['groupSpec'])) {
            $body['groupSpec'] = $params['groupSpec'];
        }

        if (isset($params['maxPassages'])) {
            $body['maxPassages'] = $params['maxPassages'];
        }

        if (isset($params['region'])) {
            $body['region'] = $params['region'];
        }

        if (isset($params['l10n'])) {
            $body['l10n'] = $params['l10n'];
        }

        if (isset($params['responseFormat'])) {
            $body['responseFormat'] = $params['responseFormat'];
        }

        if (isset($params['userAgent'])) {
            $body['userAgent'] = $params['userAgent'];
        }

        if (isset($params['metadata'])) {
            $body['metadata'] = $params['metadata'];
        }

        return $body;
    }
}
