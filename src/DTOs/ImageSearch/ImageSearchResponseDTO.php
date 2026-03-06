<?php

namespace Tigusigalpa\YandexSearch\DTOs\ImageSearch;

class ImageSearchResponseDTO
{
    public function __construct(
        public readonly array $images,
        public readonly int $total,
        public readonly ?string $nextPageToken = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        $images = [];
        if (isset($data['images']) && is_array($data['images'])) {
            foreach ($data['images'] as $image) {
                $images[] = ImageInfoDTO::fromArray($image);
            }
        }

        return new self(
            images: $images,
            total: $data['total'] ?? count($images),
            nextPageToken: $data['nextPageToken'] ?? null
        );
    }

    public static function fromXml(\SimpleXMLElement $xml): self
    {
        $response = $xml->response;
        
        $images = [];
        if (isset($response->results->grouping->group)) {
            foreach ($response->results->grouping->group as $group) {
                foreach ($group->doc as $doc) {
                    $images[] = ImageInfoDTO::fromXml($doc);
                }
            }
        }

        $total = isset($response->found) ? (int) $response->found : count($images);

        return new self(
            images: $images,
            total: $total
        );
    }
}
