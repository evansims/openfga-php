<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;

final class TupleKey implements TupleKeyInterface
{
    public function __construct(
        private TupleKeyType $type,
        private ?string $user = null,
        private ?string $relation = null,
        private ?string $object = null,
        private ?ConditionInterface $condition = null,
    ) {
        $this->validateProperties();
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

    public function getType(): TupleKeyType
    {
        return $this->type;
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
        $data = self::validatedTupleKeyShape($type, $data);

        return new self(
            type: $type,
            user: $data['user'] ?? null,
            relation: $data['relation'] ?? null,
            object: $data['object'] ?? null,
            condition: isset($data['condition']) ? Condition::fromArray($data['condition']) : null,
        );
    }

    /**
     * Validates the shape of the array to be used as tuple key data. Throws an exception if the data is invalid.
     *
     * @param array{user?: string, relation?: string, object?: string, condition?: ConditionShape} $data
     * @param TupleKeyType                                                                         $type
     *
     * @throws InvalidArgumentException
     *
     * @return TupleKeyShape
     */
    public static function validatedTupleKeyShape(TupleKeyType $type, array $data): array
    {
        return $data;
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
                throw new InvalidArgumentException("Missing required tuple key property `{$key}`");
            }
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

    private static function supported(TupleKeyType $type): array
    {
        switch ($type) {
            case TupleKeyType::ASSERTION_TUPLE_KEY:
                return ['user', 'relation', 'object'];
            default:
                return ['user', 'relation', 'object', 'condition'];
        }
    }
}
