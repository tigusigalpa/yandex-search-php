<?php

namespace Tigusigalpa\YandexSearch\DTOs\WebSearch;

class OperationDTO
{
    public function __construct(
        public readonly string $id,
        public readonly bool $done,
        public readonly ?string $createdAt = null,
        public readonly ?string $createdBy = null,
        public readonly ?string $modifiedAt = null,
        public readonly ?array $metadata = null,
        public readonly mixed $response = null,
        public readonly ?array $error = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? '',
            done: $data['done'] ?? false,
            createdAt: $data['createdAt'] ?? null,
            createdBy: $data['createdBy'] ?? null,
            modifiedAt: $data['modifiedAt'] ?? null,
            metadata: $data['metadata'] ?? null,
            response: $data['response'] ?? null,
            error: $data['error'] ?? null
        );
    }
}
