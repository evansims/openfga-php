<?php

declare(strict_types=1);

namespace OpenFGA\Options;

use DateTimeImmutable;

interface ListTupleChangesOptionsInterface extends OptionsInterface
{
    public function getContinuationToken(): ?string;

    public function getPageSize(): ?int;

    public function getStartTime(): ?DateTimeImmutable;

    public function getType(): ?string;
}
