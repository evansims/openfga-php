<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class AuthorizationModel extends Model implements AuthorizationModelInterface
{
    public function __construct(
        public string $id,
        public string $schemaVersion,
        public TypeDefinitions $typeDefinitions,
        public ?Conditions $conditions = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'tuple_key' => $this->tupleKey->toArray(),
            'expectation' => $this->expectation,
            'contextual_tuples' => $this->contextualTuples?->toArray(),
            'context' => $this->context,
        ];
    }

    public static function fromArray(array $data): self
    {
        $tupleKey = $data['tuple_key'] ?? null;
        $expectation = $data['expectation'] ?? null;
        $contextualTuples = $data['contextual_tuples'] ?? null;
        $context = $data['context'] ?? null;

        $tupleKey = $tupleKey ? AssertionTupleKey::fromArray($tupleKey) : null;
        $expectation = $expectation ? (bool)$expectation : false;
        $contextualTuples = $contextualTuples ? TupleKeys::fromArray($contextualTuples) : null;
        $context = $context ? (array)$context : null;

        return new self(
            tupleKey: $tupleKey,
            expectation: $expectation,
            contextualTuples: $contextualTuples,
            context: $context,
        );
    }
}
