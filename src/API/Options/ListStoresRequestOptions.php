<?php

declare(strict_types=1);

namespace OpenFGA\API\Options;

use OpenFGA\API\RequestOptions;

final class ListStoresRequestOptions extends RequestOptions
{
    public function __construct(
        public string $continuationToken = '',
        public int $pageSize = 1,
    ) {
    }
}
