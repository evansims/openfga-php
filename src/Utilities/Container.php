<?php

declare(strict_types=1);

namespace OpenFGA\Utilities;

use OpenFGA\Exceptions\Containers\ServiceNotFoundException;
use Psr\Container\ContainerInterface;

final class Container implements ContainerInterface
{
    /**
     * @param array<string,mixed> $services
     */
    public function __construct(
        private array $services = [],
    ) {
    }

    public function get(string $id): mixed
    {
        if ($this->has($id)) {
            return $this->services[$id];
        }

        throw new ServiceNotFoundException();
    }

    public function has(string $id): bool
    {
        return (bool) (isset($this->services[$id]));
    }

    public function set(string $id, mixed $data): void
    {
        $this->services[$id] = $data;
    }
}
