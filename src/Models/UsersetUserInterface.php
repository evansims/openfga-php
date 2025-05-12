<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface UsersetUserInterface extends ModelInterface
{
    public function getId(): string;

    public function getRelation(): string;

    public function getType(): string;
}
