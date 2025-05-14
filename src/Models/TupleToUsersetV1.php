<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;

use function is_array;

final class TupleToUsersetV1 implements TupleToUsersetV1Interface
{
    public const string OPENAPI_TYPE = 'v1.TupleToUserset';

    public function __construct(
        private ObjectRelationInterface $tupleset,
        private ObjectRelationInterface $computedUserset,
    ) {
    }

    public function getComputedUserset(): ObjectRelationInterface
    {
        return $this->computedUserset;
    }

    public function getTupleset(): ObjectRelationInterface
    {
        return $this->tupleset;
    }

    public function jsonSerialize(): array
    {
        return [
            'tupleset' => $this->tupleset->jsonSerialize(),
            'computed_userset' => $this->computedUserset->jsonSerialize(),
        ];
    }

    public static function fromArray(array $data): self
    {
        $data = self::validatedTupleToUsersetShape($data);

        return new self(
            tupleset: ObjectRelation::fromArray($data['tupleset']),
            computedUserset: ObjectRelation::fromArray($data['computed_userset']),
        );
    }

    /**
     * Validates the shape of the array to be used as tuple to userset data. Throws an exception if the data is invalid.
     *
     * @param array{tupleset: ObjectRelationShape, computed_userset: ObjectRelationShape} $data
     *
     * @throws InvalidArgumentException
     *
     * @return TupleToUsersetShape
     */
    public static function validatedTupleToUsersetShape(array $data): array
    {
        if (! isset($data['tupleset'], $data['computed_userset']) || ! is_array($data['tupleset']) || ! is_array($data['computed_userset'])) {
            throw new InvalidArgumentException('Invalid tuple to userset data structure');
        }

        return $data;
    }
}
