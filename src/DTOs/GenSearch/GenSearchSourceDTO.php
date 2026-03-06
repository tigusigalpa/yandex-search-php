<?php

namespace Tigusigalpa\YandexSearch\DTOs\GenSearch;

class GenSearchSourceDTO
{
    public function __construct(
        public readonly string $url,
        public readonly string $title,
        public readonly bool $used
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            url: $data['url'] ?? '',
            title: $data['title'] ?? '',
            used: $data['used'] ?? false
        );
    }
}
