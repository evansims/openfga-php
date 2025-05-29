<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use Override;

interface UsersetTreeTupleToUsersetInterface extends ModelInterface
{
    /**
     * @return array<int, ComputedInterface>
     */
    public function getComputed(): array;

    public function getTupleset(): string;

    /**
     * @return array{tupleset: string, computed: array<int, array{userset: string}>}
     */
    #[Override]
    public function jsonSerialize(): array;
}
