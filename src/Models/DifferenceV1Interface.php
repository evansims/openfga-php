<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use Override;

interface DifferenceV1Interface extends ModelInterface
{
    public function getBase(): UsersetInterface;

    public function getSubtract(): UsersetInterface;

    /**
     * @return array{base: array{
     *     computedUserset?: array{object?: string, relation?: string},
     *     tupleToUserset?: array{tupleset: array{object?: string, relation?: string}, computedUserset: array{object?: string, relation?: string}},
     *     union?: array<mixed>,
     *     intersection?: array<mixed>,
     *     difference?: array{base: array<mixed>, subtract: array<mixed>},
     *     this?: object,
     * }, subtract: array{
     *     computedUserset?: array{object?: string, relation?: string},
     *     tupleToUserset?: array{tupleset: array{object?: string, relation?: string}, computedUserset: array{object?: string, relation?: string}},
     *     union?: array<mixed>,
     *     intersection?: array<mixed>,
     *     difference?: array{base: array<mixed>, subtract: array<mixed>},
     *     this?: object,
     * }}
     */
    #[Override]
    public function jsonSerialize(): array;
}
