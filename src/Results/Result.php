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
    public function unwrap(?callable $fn = null): mixed
    {
        if ($this->failed()) {
            if ($fn !== null) {
                return $fn($this->err());
            }

            throw $this->err();
        }

        if ($fn !== null) {
            return $fn($this->val());
        }

        return $this->val();
    }
}
