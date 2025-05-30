<?php

declare(strict_types=1);

namespace OpenFGA\Results;

use Override;

/**
 * Abstract base class providing shared functionality for Result implementations.
 *
 * This class implements common methods that are shared between Success and Failure
 * results, reducing code duplication while maintaining the Result pattern's
 * type safety and fluent interface.
 */
abstract class Result implements ResultInterface
{
    /**
     * @inheritDoc
     */
    #[Override]
    public function unwrap(?callable $fn = null): mixed
    {
        if ($this->failed()) {
            if (null !== $fn) {
                $error = $this->err();

                return $fn($error);
            }

            throw $this->err();
        }

        if (null !== $fn) {
            /** @var mixed $value */
            $value = $this->val();

            return $fn($value);
        }

        return $this->val();
    }
}
