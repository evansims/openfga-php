<?php

declare(strict_types=1);

namespace OpenFGA\Options;

use OpenFGA\Models\Consistency;

final class ListUsersOptions implements ListUsersOptionsInterface
{
    use OptionsTrait;

    public function __construct(
        private ?Consistency $consistency = null,
    ) {
    }

    public function getConsistency(): ?Consistency
    {
        return $this->consistency;
    }
}
