<?php

declare(strict_types=1);

namespace OpenFGA\Results;

use Override;
use Throwable;

/**
 * @template T
 * @template E of Throwable
 *
 * @implements ResultInterface<T, E>
 */
abstract class Result implements ResultInterface
{
    #[Override]
    /**
     * @inheritDoc
     */
    public function unwrap(mixed $default = null): mixed
    {
        return $this->succeeded() ? $this->val() : $default;
    }
}
