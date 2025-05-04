<?php

declare(strict_types=1);

namespace OpenFGA\Credentials;

interface CredentialInterface
{
    public function validate(): void;
}
