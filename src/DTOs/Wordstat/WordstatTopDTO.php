<?php

namespace Tigusigalpa\YandexSearch\DTOs\Wordstat;

class WordstatTopDTO
{
    public function __construct(
        public readonly string $phrase,
        public readonly array $topPhrases
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            phrase: $data['phrase'] ?? '',
            topPhrases: $data['topPhrases'] ?? []
        );
    }
}
