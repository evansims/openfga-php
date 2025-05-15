<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\{TupleKeysInterface, UserTypeFiltersInterface};
use OpenFGA\Options\ListUsersOptionsInterface;

interface ListUsersRequestInterface extends RequestInterface
{
    public function getAuthorizationModel(): string;

    public function getContext(): ?object;

    public function getContextualTuples(): ?TupleKeysInterface;

    public function getOptions(): ?ListUsersOptionsInterface;

    public function getRelation(): string;

    public function getStore(): string;

    public function getUserFilters(): UserTypeFiltersInterface;
}
