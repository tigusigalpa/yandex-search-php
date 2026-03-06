<?php

namespace Tigusigalpa\YandexSearch\Resources;

use Tigusigalpa\YandexSearch\DTOs\Wordstat\WordstatTopDTO;
use Tigusigalpa\YandexSearch\DTOs\Wordstat\WordstatDynamicsDTO;
use Tigusigalpa\YandexSearch\DTOs\Wordstat\WordstatRegionsDistributionDTO;
use Tigusigalpa\YandexSearch\DTOs\Wordstat\WordstatRegionTreeDTO;

class WordstatResource extends BaseResource
{
    public function getTop(string $phrase, array $params = []): WordstatTopDTO
    {
        $body = [
            'folderId' => $this->folderId,
            'phrase' => $phrase,
        ];

        if (isset($params['numPhrases'])) {
            $body['numPhrases'] = $params['numPhrases'];
        }

        if (isset($params['regions'])) {
            $body['regions'] = $params['regions'];
        }

        if (isset($params['devices'])) {
            $body['devices'] = $params['devices'];
        }

        $response = $this->request('POST', '/v2/wordstat/topRequests', [
            'json' => $body,
        ]);

        $data = json_decode((string) $response->getBody(), true);

        return WordstatTopDTO::fromArray($data);
    }

    public function getDynamics(string $phrase, array $params = []): WordstatDynamicsDTO
    {
        $body = [
            'folderId' => $this->folderId,
            'phrase' => $phrase,
        ];

        if (isset($params['regions'])) {
            $body['regions'] = $params['regions'];
        }

        if (isset($params['devices'])) {
            $body['devices'] = $params['devices'];
        }

        $response = $this->request('POST', '/v2/wordstat/dynamics', [
            'json' => $body,
        ]);

        $data = json_decode((string) $response->getBody(), true);

        return WordstatDynamicsDTO::fromArray($data);
    }

    public function getRegionsDistribution(string $phrase, array $params = []): WordstatRegionsDistributionDTO
    {
        $body = [
            'folderId' => $this->folderId,
            'phrase' => $phrase,
        ];

        if (isset($params['devices'])) {
            $body['devices'] = $params['devices'];
        }

        $response = $this->request('POST', '/v2/wordstat/regionsDistribution', [
            'json' => $body,
        ]);

        $data = json_decode((string) $response->getBody(), true);

        return WordstatRegionsDistributionDTO::fromArray($data);
    }

    public function getRegionTree(array $params = []): WordstatRegionTreeDTO
    {
        $queryParams = ['folderId' => $this->folderId];

        $response = $this->request('GET', '/v2/wordstat/regionsTree?' . http_build_query($queryParams));

        $data = json_decode((string) $response->getBody(), true);

        return WordstatRegionTreeDTO::fromArray($data);
    }
}
