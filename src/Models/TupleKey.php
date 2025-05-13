<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;

final class TupleKey implements TupleKeyInterface
{
    use ModelTrait;

    public function __construct(
        private TupleKeyType $type,
        private ?string $user = null,
        private ?string $relation = null,
        private ?string $object = null,
        private ?ConditionInterface $condition = null,
    ) {
        $this->validateProperties();
    }

    public function getType(): TupleKeyType
    {
        return $this->type;
    }

    public function getCondition(): ?ConditionInterface
    {
        return $this->condition;
    }

    public function getObject(): ?string
    {
        return $this->object;
    }

    public function getRelation(): ?string
    {
        return $this->relation;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function jsonSerialize(): array
    {
        $response = [];

        if (null !== $this->getUser()) {
            $response['user'] = $this->getUser();
        }

        if (null !== $this->getRelation()) {
            $response['relation'] = $this->getRelation();
        }

        if (null !== $this->getObject()) {
            $response['object'] = $this->getObject();
        }

        if ($this->getCondition()) {
            $response['condition'] = $this->getCondition()->jsonSerialize();
        }

        return $response;
    }

    public static function fromArray(TupleKeyType $type, array $data): self
    {
        $user = $data['user'] ?? null;
        $relation = $data['relation'] ?? null;
        $object = $data['object'] ?? null;
        $condition = isset($data['condition']) ? Condition::fromArray($data['condition']) : null;

        return new self(
            type: $type,
            user: $user,
            relation: $relation,
            object: $object,
            condition: $condition,
        );
    }

    private static function supported(TupleKeyType $type): array
    {
        switch ($type) {
            case TupleKeyType::ASSERTION_TUPLE_KEY:
                return ['user', 'relation', 'object'];
            default:
                return ['user', 'relation', 'object', 'condition'];
        }
    }

    private static function required(TupleKeyType $type): array
    {
        switch ($type) {
            case TupleKeyType::ASSERTION_TUPLE_KEY:
                return ['user', 'relation', 'object'];
            default:
                return [];
        }
    }

    private function validateProperties(): void
    {
        $supported = self::supported($this->getType());
        $required = self::required($this->getType());

        if (! isset($supported['user']) && null !== $this->user) {
            throw new InvalidArgumentException('User is not supported for this tuple key type');
        }

        if (! isset($supported['relation']) && null !== $this->relation) {
            throw new InvalidArgumentException('Relation is not supported for this tuple key type');
        }

        if (! isset($supported['object']) && null !== $this->object) {
            throw new InvalidArgumentException('Object is not supported for this tuple key type');
        }

        if (! isset($supported['condition']) && null !== $this->condition) {
            throw new InvalidArgumentException('Condition is not supported for this tuple key type');
        }

        foreach ($required as $key) {
            if (null === $this->{$key}) {
                throw new InvalidArgumentException("Missing required tuple key property `$key`");
            }
        }
    }
}
