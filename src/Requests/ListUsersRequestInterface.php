<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\Collections\{TupleKeysInterface, UserTypeFiltersInterface};
use OpenFGA\Models\Enums\Consistency;
use OpenFGA\Models\{TupleKeyInterface, UserTypeFilterInterface};

interface ListUsersRequestInterface extends RequestInterface
{
    public function getAuthorizationModel(): string;

    public function getConsistency(): ?Consistency;

    public function getContext(): ?object;

    /**
     * @return TupleKeysInterface<TupleKeyInterface>
     */
    public function getContextualTuples(): ?TupleKeysInterface;

    public function getRelation(): string;

    public function getStore(): string;

    /**
     * @return UserTypeFiltersInterface<UserTypeFilterInterface>
     */
    public function getUserFilters(): UserTypeFiltersInterface;
}
