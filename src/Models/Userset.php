<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\{Usersets, UsersetsInterface};

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};
use Override;
use stdClass;

final class Userset implements UsersetInterface
{
    public const OPENAPI_TYPE = 'Userset';

    private static ?SchemaInterface $schema = null;

    /**
     * @param null|object                              $direct
     * @param null|ObjectRelationInterface             $computedUserset
     * @param null|TupleToUsersetV1Interface           $tupleToUserset
     * @param null|UsersetsInterface<UsersetInterface> $union
     * @param null|UsersetsInterface<UsersetInterface> $intersection
     * @param null|DifferenceV1Interface               $difference
     */
    public function __construct(
        private readonly ?object $direct = null,
        private readonly ?ObjectRelationInterface $computedUserset = null,
        private readonly ?TupleToUsersetV1Interface $tupleToUserset = null,
        private readonly ?UsersetsInterface $union = null,
        private readonly ?UsersetsInterface $intersection = null,
        private readonly ?DifferenceV1Interface $difference = null,
    ) {
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getComputedUserset(): ?ObjectRelationInterface
    {
        return $this->computedUserset;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getDifference(): ?DifferenceV1Interface
    {
        return $this->difference;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getDirect(): ?object
    {
        return $this->direct;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getIntersection(): ?UsersetsInterface
    {
        return $this->intersection;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getTupleToUserset(): ?TupleToUsersetV1Interface
    {
        return $this->tupleToUserset;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getUnion(): ?UsersetsInterface
    {
        return $this->union;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function jsonSerialize(): array
    {
        $data = [];

        if (null !== $this->direct) {
            // 'this' should be an empty object in JSON
            $data['this'] = new stdClass();
        }

        if ($this->computedUserset instanceof ObjectRelationInterface) {
            $data['computedUserset'] = $this->computedUserset->jsonSerialize();
        }

        if ($this->tupleToUserset instanceof TupleToUsersetV1Interface) {
            $data['tupleToUserset'] = $this->tupleToUserset->jsonSerialize();
        }

        if ($this->union instanceof UsersetsInterface) {
            $data['union'] = $this->union->jsonSerialize();
        }

        if ($this->intersection instanceof UsersetsInterface) {
            $data['intersection'] = $this->intersection->jsonSerialize();
        }

        if ($this->difference instanceof DifferenceV1Interface) {
            $data['difference'] = $this->difference->jsonSerialize();
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'this', type: 'object', required: false, parameterName: 'direct'),
                new SchemaProperty(name: 'computedUserset', type: 'object', className: ObjectRelation::class, required: false),
                new SchemaProperty(name: 'tupleToUserset', type: 'object', className: TupleToUsersetV1::class, required: false),
                new SchemaProperty(name: 'union', type: 'object', className: Usersets::class, required: false),
                new SchemaProperty(name: 'intersection', type: 'object', className: Usersets::class, required: false),
                new SchemaProperty(name: 'difference', type: 'object', className: DifferenceV1::class, required: false),
            ],
        );
    }
}
