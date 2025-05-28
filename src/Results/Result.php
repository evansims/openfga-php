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
            if (null !== $fn) {
                /** @var E $error */
                $error = $this->err();

                return $fn($error);
            }

            throw $this->err();
        }

        if (null !== $fn) {
            /** @var T $value */
            $value = $this->val();

            return $fn($value);
        }

        return $this->val();
    }
}
