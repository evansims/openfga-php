<?php

declare(strict_types=1);

namespace OpenFGA\Options;

use OpenFGA\Models\Consistency;

interface ReadTuplesOptionsInterface extends OptionsInterface
{
    public function getConsistency(): ?Consistency;

    public function getContinuationToken(): ?string;

    public function getPageSize(): ?int;
}
