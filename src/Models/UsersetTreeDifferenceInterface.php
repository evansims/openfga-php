<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface UsersetTreeDifferenceInterface extends ModelInterface
{
    public function getBase(): NodeInterface;

    public function getSubtract(): NodeInterface;

    /**
     * @return array{base: array{
     *     computed_userset?: array{object?: string, relation?: string},
     *     tuple_to_userset?: array{tupleset: array{object?: string, relation?: string}, computed_userset: array{object?: string, relation?: string}},
     *     union?: array<mixed>,
     *     intersection?: array<mixed>,
     *     difference?: array{base: array<mixed>, subtract: array<mixed>},
     *     direct?: object,
     * }, subtract: array{
     *     computed_userset?: array{object?: string, relation?: string},
     *     tuple_to_userset?: array{tupleset: array{object?: string, relation?: string}, computed_userset: array{object?: string, relation?: string}},
     *     union?: array<mixed>,
     *     intersection?: array<mixed>,
     *     difference?: array{base: array<mixed>, subtract: array<mixed>},
     *     direct?: object,
     * }}
     */
    public function jsonSerialize(): array;
}
