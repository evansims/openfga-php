<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\Collections\TupleKeysInterface;
use OpenFGA\Models\TupleKeyInterface;

interface WriteTuplesRequestInterface extends RequestInterface
{
    public function getAuthorizationModel(): string;

    /**
     * @return null|TupleKeysInterface<TupleKeyInterface>
     */
    public function getDeletes(): ?TupleKeysInterface;

    public function getStore(): string;

    /**
     * @return null|TupleKeysInterface<TupleKeyInterface>
     */
    public function getWrites(): ?TupleKeysInterface;
}
