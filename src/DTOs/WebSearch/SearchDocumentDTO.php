<?php

namespace Tigusigalpa\YandexSearch\DTOs\WebSearch;

class SearchDocumentDTO
{
    public function __construct(
        public readonly string $url,
        public readonly string $domain,
        public readonly string $title,
        public readonly ?string $headline = null,
        public readonly ?string $passage = null,
        public readonly ?string $modtime = null,
        public readonly ?int $size = null,
        public readonly ?string $charset = null,
        public readonly ?array $properties = null
    ) {
    }

    public static function fromXml(\SimpleXMLElement $doc): self
    {
        $url = (string) $doc->url;
        $domain = (string) $doc->domain;
        $title = isset($doc->title) ? self::extractText($doc->title) : '';
        $headline = isset($doc->headline) ? self::extractText($doc->headline) : null;
        
        $passages = [];
        if (isset($doc->passages->passage)) {
            foreach ($doc->passages->passage as $passage) {
                $passages[] = self::extractText($passage);
            }
        }
        $passage = !empty($passages) ? implode(' ... ', $passages) : null;

        $modtime = isset($doc->modtime) ? (string) $doc->modtime : null;
        $size = isset($doc->size) ? (int) $doc->size : null;
        $charset = isset($doc->charset) ? (string) $doc->charset : null;

        $properties = [];
        if (isset($doc->properties->property)) {
            foreach ($doc->properties->property as $property) {
                $properties[(string) $property['name']] = (string) $property;
            }
        }

        return new self(
            url: $url,
            domain: $domain,
            title: $title,
            headline: $headline,
            passage: $passage,
            modtime: $modtime,
            size: $size,
            charset: $charset,
            properties: !empty($properties) ? $properties : null
        );
    }

    private static function extractText(\SimpleXMLElement $element): string
    {
        $text = '';
        foreach ($element->children() as $child) {
            if ($child->getName() === 'hlword') {
                $text .= (string) $child;
            } else {
                $text .= (string) $child;
            }
        }
        
        if (empty($text)) {
            $text = (string) $element;
        }

        return trim($text);
    }
}
