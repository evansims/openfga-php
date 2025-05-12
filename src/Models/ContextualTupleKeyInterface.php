<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface ContextualTupleKeyInterface extends ModelInterface
{
    public function getCondition(): string;

    public function getObject(): string;

    public function getRelation(): string;

    public function getUser(): string;
}
