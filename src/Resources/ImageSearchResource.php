<?php

namespace Tigusigalpa\YandexSearch\Resources;

use Tigusigalpa\YandexSearch\DTOs\ImageSearch\ImageSearchResponseDTO;

class ImageSearchResource extends BaseResource
{
    public function search(string $query, array $params = []): ImageSearchResponseDTO
    {
        $body = [
            'folderId' => $this->folderId,
            'query' => [
                'searchType' => $params['searchType'] ?? 'SEARCH_TYPE_RU',
                'queryText' => $query,
                'familyMode' => $params['familyMode'] ?? 'FAMILY_MODE_MODERATE',
                'page' => $params['page'] ?? 0,
            ],
        ];

        if (isset($params['imageSpec'])) {
            $body['imageSpec'] = $params['imageSpec'];
        }

        $response = $this->request('POST', '/v2/image/search', [
            'json' => $body,
        ]);

        $data = json_decode((string) $response->getBody(), true);

        if (isset($data['rawData'])) {
            $xml = $this->parseXmlResponse($data['rawData']);
            return ImageSearchResponseDTO::fromXml($xml);
        }

        return ImageSearchResponseDTO::fromArray($data);
    }

    public function searchByImage(array $imageSource, array $params = []): ImageSearchResponseDTO
    {
        $body = [
            'folderId' => $this->folderId,
        ];

        if (isset($imageSource['url'])) {
            $body['imageSource'] = ['url' => $imageSource['url']];
        } elseif (isset($imageSource['data'])) {
            $body['imageSource'] = ['data' => $imageSource['data']];
        } elseif (isset($imageSource['id'])) {
            $body['imageSource'] = ['id' => $imageSource['id']];
        }

        if (isset($params['page'])) {
            $body['page'] = $params['page'];
        }

        if (isset($params['familyMode'])) {
            $body['familyMode'] = $params['familyMode'];
        }

        $response = $this->request('POST', '/v2/image/search_by_image', [
            'json' => $body,
        ]);

        $data = json_decode((string) $response->getBody(), true);

        return ImageSearchResponseDTO::fromArray($data);
    }
}
