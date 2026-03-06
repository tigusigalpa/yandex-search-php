<?php

namespace Tigusigalpa\YandexSearch\DTOs\GenSearch;

class GenSearchResponseDTO
{
    public function __construct(
        public readonly string $answer,
        public readonly array $sources,
        public readonly array $searchQueries,
        public readonly bool $isAnswerRejected,
        public readonly bool $isBulletAnswer,
        public readonly ?array $metadata = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        $sources = [];
        if (isset($data['sources']) && is_array($data['sources'])) {
            foreach ($data['sources'] as $source) {
                $sources[] = GenSearchSourceDTO::fromArray($source);
            }
        }

        return new self(
            answer: $data['answer'] ?? '',
            sources: $sources,
            searchQueries: $data['searchQueries'] ?? [],
            isAnswerRejected: $data['isAnswerRejected'] ?? false,
            isBulletAnswer: $data['isBulletAnswer'] ?? false,
            metadata: $data['metadata'] ?? null
        );
    }
}
