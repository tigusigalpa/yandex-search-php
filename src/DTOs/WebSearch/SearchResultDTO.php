<?php

namespace Tigusigalpa\YandexSearch\DTOs\WebSearch;

class SearchResultDTO
{
    public function __construct(
        public readonly int $found,
        public readonly int $foundHuman,
        public readonly array $documents,
        public readonly ?string $reask = null,
        public readonly ?string $misspell = null,
        public readonly ?array $groupings = null
    ) {
    }

    public static function fromXml(\SimpleXMLElement $xml): self
    {
        $response = $xml->response;
        
        $found = (int) ($response->found ?? 0);
        $foundHuman = (int) ($response->{'found-human'} ?? 0);
        
        $documents = [];
        if (isset($response->results->grouping->group)) {
            foreach ($response->results->grouping->group as $group) {
                foreach ($group->doc as $doc) {
                    $documents[] = SearchDocumentDTO::fromXml($doc);
                }
            }
        }

        $reask = isset($response->reask->text) ? (string) $response->reask->text : null;
        $misspell = isset($response->misspell->text) ? (string) $response->misspell->text : null;

        return new self(
            found: $found,
            foundHuman: $foundHuman,
            documents: $documents,
            reask: $reask,
            misspell: $misspell
        );
    }
}
