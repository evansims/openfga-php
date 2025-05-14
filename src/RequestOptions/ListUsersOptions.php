<?php

declare(strict_types=1);

namespace OpenFGA\RequestOptions;

use OpenFGA\Models\Consistency;

final class ListUsersOptions extends RequestOptions
{
    use RequestOptionsTrait;

    public function __construct(
        private ?Consistency $consistency = null,
    ) {
    }

    public function getConsistency(): ?Consistency
    {
        return $this->consistency;
    }
}
