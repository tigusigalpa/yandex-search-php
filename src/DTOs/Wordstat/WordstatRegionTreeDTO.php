<?php

namespace Tigusigalpa\YandexSearch\DTOs\Wordstat;

class WordstatRegionTreeDTO
{
    public function __construct(
        public readonly array $regions
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            regions: $data['regions'] ?? []
        );
    }
}
