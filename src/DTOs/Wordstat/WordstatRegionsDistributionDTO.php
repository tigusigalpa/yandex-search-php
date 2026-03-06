<?php

namespace Tigusigalpa\YandexSearch\DTOs\Wordstat;

class WordstatRegionsDistributionDTO
{
    public function __construct(
        public readonly string $phrase,
        public readonly array $regionsDistribution
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            phrase: $data['phrase'] ?? '',
            regionsDistribution: $data['regionsDistribution'] ?? []
        );
    }
}
