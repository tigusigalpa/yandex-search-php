<?php

namespace Tigusigalpa\YandexSearch\DTOs\ImageSearch;

class ImageInfoDTO
{
    public function __construct(
        public readonly string $url,
        public readonly ?string $format = null,
        public readonly ?int $width = null,
        public readonly ?int $height = null,
        public readonly ?string $passage = null,
        public readonly ?string $host = null,
        public readonly ?string $pageTitle = null,
        public readonly ?string $pageUrl = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            url: $data['url'] ?? '',
            format: $data['format'] ?? null,
            width: isset($data['width']) ? (int) $data['width'] : null,
            height: isset($data['height']) ? (int) $data['height'] : null,
            passage: $data['passage'] ?? null,
            host: $data['host'] ?? null,
            pageTitle: $data['pageTitle'] ?? null,
            pageUrl: $data['pageUrl'] ?? null
        );
    }

    public static function fromXml(\SimpleXMLElement $doc): self
    {
        $url = (string) $doc->url;
        
        $properties = [];
        if (isset($doc->properties->property)) {
            foreach ($doc->properties->property as $property) {
                $properties[(string) $property['name']] = (string) $property;
            }
        }

        $format = $properties['format'] ?? null;
        $width = isset($properties['width']) ? (int) $properties['width'] : null;
        $height = isset($properties['height']) ? (int) $properties['height'] : null;

        $passage = null;
        if (isset($doc->passages->passage)) {
            $passages = [];
            foreach ($doc->passages->passage as $p) {
                $passages[] = (string) $p;
            }
            $passage = implode(' ', $passages);
        }

        $host = isset($doc->domain) ? (string) $doc->domain : null;
        $pageTitle = isset($doc->title) ? (string) $doc->title : null;
        $pageUrl = isset($doc->{'page-url'}) ? (string) $doc->{'page-url'} : null;

        return new self(
            url: $url,
            format: $format,
            width: $width,
            height: $height,
            passage: $passage,
            host: $host,
            pageTitle: $pageTitle,
            pageUrl: $pageUrl
        );
    }
}
