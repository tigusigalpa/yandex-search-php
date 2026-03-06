<?php

namespace Tigusigalpa\YandexSearch\Resources;

use Tigusigalpa\YandexSearch\DTOs\GenSearch\GenSearchResponseDTO;

class GenSearchResource extends BaseResource
{
    public function search(array $messages, array $params = []): GenSearchResponseDTO
    {
        $body = [
            'folderId' => $this->folderId,
            'messages' => $messages,
        ];

        if (isset($params['site'])) {
            $body['site'] = $params['site'];
        }

        if (isset($params['host'])) {
            $body['host'] = $params['host'];
        }

        if (isset($params['url'])) {
            $body['url'] = $params['url'];
        }

        if (isset($params['fixMisspell'])) {
            $body['fixMisspell'] = $params['fixMisspell'];
        }

        if (isset($params['enableNrfmDocs'])) {
            $body['enableNrfmDocs'] = $params['enableNrfmDocs'];
        }

        if (isset($params['searchFilters'])) {
            $body['searchFilters'] = $params['searchFilters'];
        }

        if (isset($params['searchType'])) {
            $body['searchType'] = $params['searchType'];
        }

        if (isset($params['getPartialResults'])) {
            $body['getPartialResults'] = $params['getPartialResults'];
        }

        $response = $this->request('POST', '/v2/gen/search', [
            'json' => $body,
        ]);

        $data = json_decode((string) $response->getBody(), true);

        return GenSearchResponseDTO::fromArray($data);
    }
}
