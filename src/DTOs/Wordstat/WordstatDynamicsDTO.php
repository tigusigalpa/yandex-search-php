<?php

namespace Tigusigalpa\YandexSearch\DTOs\Wordstat;

class WordstatDynamicsDTO
{
    public function __construct(
        public readonly string $phrase,
        public readonly array $dynamics
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            phrase: $data['phrase'] ?? '',
            dynamics: $data['dynamics'] ?? []
        );
    }
}
