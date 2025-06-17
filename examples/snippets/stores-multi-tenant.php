<?php

declare(strict_types=1);

use OpenFGA\Client;

$client = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
);

// example: usage
use function OpenFGA\store;

final class TenantStoreManager
{
    public function __construct(
        private readonly Client $client,
        private array $cache = [],
    ) {
    }

    public function getStoreForTenant(string $tenantId): string
    {
        if (! array_key_exists($tenantId, $this->cache)) {
            $this->cache[$tenantId] = store("tenant-{$tenantId}", $this->client);
        }

        return $this->cache[$tenantId];
    }
}

$tenantManager = new TenantStoreManager($client);

$acmeStoreId = $tenantManager->getStoreForTenant('acme-corp');
echo "Store for ACME Corp: {$acmeStoreId}\n";
// end-example: usage
