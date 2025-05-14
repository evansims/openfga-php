<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;

final class UserTypeFilter implements UserTypeFilterInterface
{
    public function __construct(
        private string $type,
        private ?string $relation = null,
    ) {
    }

    public function getRelation(): ?string
    {
        return $this->relation;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function jsonSerialize(): array
    {
        $response = [
            'type' => $this->type,
        ];

        if (null !== $this->relation) {
            $response['relation'] = $this->relation;
        }

        return $response;
    }

    public static function fromArray(array $data): self
    {
        $data = self::validatedUserTypeFilterShape($data);

        return new self(
            type: $data['type'],
            relation: $data['relation'] ?? null,
        );
    }

    /**
     * @param array{type: string, relation?: string} $data
     *
     * @return UserTypeFilterShape
     */
    public static function validatedUserTypeFilterShape(array $data): array
    {
        if (! isset($data['type'])) {
            throw new InvalidArgumentException('UserTypeFilterShape must have a type');
        }

        return $data;
    }
}
