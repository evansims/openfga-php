<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * Represents a relation of a particular object type (e.g. 'document#viewer').
 *
 * @see https://openfga.dev/docs/reference/for-developers/api/model#relationreference
 */
final class RelationReference extends Model implements RelationReferenceInterface
{
    /**
     * Constructs a new RelationReference object.
     *
     * @param string        $type
     * @param null|string   $relation
     * @param null|Wildcard $wildcard
     * @param null|string   $condition The name of a condition that is enforced over the allowed relation.
     */
    public function __construct(
        public string $type,
        public ?string $relation = null,
        public ?Wildcard $wildcard = null,
        public ?string $condition = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'relation' => $this->relation,
            'wildcard' => $this->wildcard?->toArray() ?? null,
            'condition' => $this->condition,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            type: $data['type'],
            relation: $data['relation'],
            wildcard: $data['wildcard'] ? Wildcard::fromArray($data['wildcard']) : null,
            condition: $data['condition'],
        );
    }
}
