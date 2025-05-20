<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\ComputedsInterface;

interface UsersetTreeTupleToUsersetInterface extends ModelInterface
{
    /**
     * @return ComputedsInterface<ComputedInterface>
     */
    public function getComputed(): ComputedsInterface;

    public function getTupleset(): string;

    /**
     * @return array{tupleset: string, computed: array<int, array{userset: string}}
     */
    public function jsonSerialize(): array;
}
